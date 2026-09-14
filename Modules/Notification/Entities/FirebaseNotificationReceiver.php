<?php

namespace Modules\Notification\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use Modules\Notification\Entities\Traits\TranslatableHelper;
use Modules\Notification\Entities\Traits\Disabable;

class FirebaseNotificationReceiver extends Model
{
    protected static $preparedToSend = [];

    protected $table    = 'notif_notification_receivers';

    public $timestamps  = true;

    protected $fillable = [
        'triggered_by',
        'notification_id',
        'from_user_id',
        'to_user_id',
        'status',
    ];

    public function Notification()
    {
        return $this->belongsTo(FirebaseNotification::class, 'notification_id', 'id');
    }

    public function FromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id', 'id');
    }

    public function ToUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', 'id');
    }

    public function ToTokens()
    {
        return $this->hasMany(FirebaseToken::class, 'user_id', 'to_user_id');
    }

    public function Data()
    {
        return $this->hasMany(FirebaseNotificationReceiverData::class, 'receiver_id', 'id');
    }

    public static function prepareToSend(FirebaseNotificationReceiver $Receiver)
    {
        static::$preparedToSend[] = $Receiver->id;
    }

    public static function startSending()
    {
        if (empty(static::$preparedToSend)) {
            return false;
        }
        try {
            $Receivers = FirebaseNotificationReceiver::whereIn('id', static::$preparedToSend)
            ->with('Notification.translations', 'Data', 'ToTokens')
            ->with([
                'FromUser' => function($FromUser){
                    $FromUser->withDisabled()->withTrashed();
                }
            ])
            ->get();
            foreach ($Receivers as $key => $Receiver) {
                if (empty($Receiver->Notification)) {
                    continue;
                }
                $AddtionalData        = [];
                foreach ($Receiver->Data as $DataKey => $Content) {
                    $AddtionalData[ $Content['x_key'] ] = $Content['x_val'];
                }
                $AddtionalData['system_type'] = $Receiver->Notification->type;
                $RawNotificationsData = FirebaseNotification::prepareToSend($Receiver->Notification, $Receiver);
                if (!empty($Receiver->group)) {
                    FirebaseToken::where(function($query)use($Receiver) {
                        $query->whereHas('User', function($User)use($Receiver){
                            $User->whereHas('roles', function($role) use($Receiver) {
                                $role->where('name', $Receiver->group);
                            });
                        })->when($Receiver->group == 'CLIENT' || empty($Receiver->group), function($q) {
                            $q->orWhereNull('user_id');
                        });
                    })
                    ->orderBy('last_activity', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->chunk(500, function($_500)use($RawNotificationsData, $AddtionalData){
                        $CurlResponse = FirebaseNotification::send($_500->where('platform', 'ANDROID')->pluck('token')->toArray(), $RawNotificationsData, $AddtionalData, false);
                        $CurlResponse = FirebaseNotification::send($_500->where('platform', '!=', 'ANDROID')->pluck('token')->toArray(), $RawNotificationsData, $AddtionalData, true);
                    });
                }
                else if (empty($Receiver->to_user_id)) {
                    FirebaseToken::orderBy('last_activity', 'DESC')->orderBy('id', 'DESC')->chunk(500, function($_500)use($RawNotificationsData, $AddtionalData){
                        $CurlResponse = FirebaseNotification::send($_500->where('platform', 'ANDROID')->pluck('token')->toArray(), $RawNotificationsData, $AddtionalData, false);
                        $CurlResponse = FirebaseNotification::send($_500->where('platform', '!=', 'ANDROID')->pluck('token')->toArray(), $RawNotificationsData, $AddtionalData, true);
                    });
                }
                else {
                    $CurlResponse = FirebaseNotification::send($Receiver->ToTokens->where('platform', 'ANDROID')->pluck('token')->toArray(), $RawNotificationsData, $AddtionalData, false);
                    $CurlResponse = FirebaseNotification::send($Receiver->ToTokens->where('platform', '!=', 'ANDROID')->pluck('token')->toArray(), $RawNotificationsData, $AddtionalData, true);
                }
            }
        }
        catch (\Exception $th) {
            return false;
        }
        static::$preparedToSend = [];
    }
}
