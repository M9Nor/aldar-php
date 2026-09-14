<?php

namespace Modules\Notification\Entities;

use Carbon\Carbon;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Notification\Entities\Traits\TranslatableHelper;
use Modules\Notification\Entities\Traits\Disabable;
use Modules\Notification\Entities\Config;
use Illuminate\Support\Str;

class FirebaseNotification extends Model
{
    use Translatable,
        TranslatableHelper;

    protected $table    = 'notif_notifications';

    public $timestamps  = true;

    protected $fillable = [
        'added_by',
        'type',
    ];

    public $translationModel = 'Modules\Notification\Entities\FirebaseNotificationTranslation';

    public $translatedAttributes = [
        'title',
        'body',
        'icon',
        'image',
        'sound',
    ];
    protected static $imageOptions = [
        'dimensions' => [
            'preview' => '360x180',
        ],
    ];
    public function getImage($size = '360x180'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->image, 'http')){
            return $this->image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->image)
        ? route('image', ['size' => $size, 'path' => $this->image])
        : route('image', ['size' => $size, 'path' => 'defaults/base.png']);
    }
    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }
    public function scopeAdvancedSearch($query, $Filter, $Preffix = '')
    {
        if (is_array($Filter)) {
            if ( isset($Filter['roles']) ) {
                if (!empty($Filter['roles'])) {
                    if (is_array($Filter['roles'])) {
                        $query->whereIn('group', $Filter['roles']);
                    }
                    else {
                        $query->where('group', $Filter['roles']);
                    }
                }
            }
            if ( isset($Filter['title']) ) {
                if (!empty($Filter['title'])) {
                    $query->whereHas('translations', function($q)use($Filter){
                        $q->where('title', 'like', "%{$Filter['title']}%");
                    });
                }
            }
            if ( isset($Filter['body']) ) {
                if (!empty($Filter['body'])) {
                    $query->whereHas('translations', function($q)use($Filter){
                        $q->where('body', 'like', "%{$Filter['body']}%");
                    });
                }
            }
        }
        return $query;
    }

    public static function prepareToSend(FirebaseNotification $Notification, FirebaseNotificationReceiver $Receiver, $Locale = null)
    {
        $Locale = empty($Locale) ? (empty($Receiver->toUser) ? app()->getLocale() : $Receiver->toUser->locale) : $Locale;
        $icon = $Notification->__('icon' , $Locale);
        $image = $Notification->__('image' , $Locale);
        if ($icon) {
            $icon = asset('firebase/icons/' . $icon);
        }
        if ($image) {
            $image = $Notification->getImage();
        }
        $Result = [
            'title'       => empty($Notification->__('title', $Locale)) ? '' : $Notification->__('title', $Locale),
            'body'        => empty($Notification->__('body' , $Locale)) ? '' : $Notification->__('body' , $Locale),
            'icon'        => $icon ? $icon : Config::of('system_logo', \Module::asset('admin:metronic/media/logos/logo-light.png')),
            'image'        => $image ? $image : null,
            'date'        => Carbon::parse($Receiver->created_at)->format('Y-m-d H:i'),
            'type'        => empty($Notification->type) ? '' : $Notification->type,
            // 'click_action' => "http://test.com"
        ];
        if (!empty($Sound = $Notification->__('sound', $Locale))) {
            $Result['sound'] = asset('firebase/sounds/' . $Sound);
        }

        $Link = app()->make('Cms')->getNotificationLink($Notification->type, $Receiver->Data->toArray());
        if (!is_null( $Link )) {
            $Result['click_action'] = $Link;
        }
        else if (!empty($Notification->link)) {
            $Result['click_action'] = $Notification->link;
        }
        foreach ($Receiver->Data as $DI => $Content) {
            $Result['title'] = str_replace('{{'. $Content['x_key'] .'}}', $Content['x_val'], $Result['title']);
            $Result['body']  = str_replace('{{'. $Content['x_key'] .'}}', $Content['x_val'], $Result['body']);
            if (!empty($Result['icon'])) {
                $Result['icon']  = str_replace('{{'. $Content['x_key'] .'}}', $Content['x_val'], $Result['icon']);
            }
            if (!empty($Result['image'])) {
                $Result['image']  = str_replace('{{'. $Content['x_key'] .'}}', $Content['x_val'], $Result['image']);
            }
            if (isset($Result['sound'])) {
                $Result['sound']  = str_replace('{{'. $Content['x_key'] .'}}', $Content['x_val'], $Result['sound']);
            }
        }
        return $Result;
    }

    public static function send($Tokens, $Data, $AdditionalData = [], $SendAsNotificationKey = true)
    {
        if (empty($Tokens) OR empty($Data)) {
            return false;
        }
        if ($SendAsNotificationKey) {
            $ReadyData = [
                // 'to'               => $Tokens,
            // 'to'               => $Tokens,
                // 'to'               => $Tokens,
            // 'to'               => $Tokens,
                // 'to'               => $Tokens,
            // 'to'               => $Tokens,
                // 'to'               => $Tokens,
            // 'to'               => $Tokens,
                // 'to'               => $Tokens,
                'registration_ids' => $Tokens,
                'notification'     => $Data,
            ];
            if (!empty($AdditionalData)) {
                if (is_array($AdditionalData)) {
                    $ReadyData['data'] = $AdditionalData;
                }
            }
        }
        else {
            $ReadyData = [
                // 'to'               => $Tokens,
                'registration_ids' => $Tokens,
                // 'notification'     => [],
            ];
            $NOTIFICATION      = array_merge($AdditionalData, $Data);
            $ReadyData['data'] = $NOTIFICATION;
        }

        // [أمان 2026-09-14] تعطيل تفريغ debug كان يسرّب توكنات الأجهزة: file_put_contents(public_path('firebase-sent-data-2.txt'), json_encode($ReadyData, JSON_PRETTY_PRINT));
        $HEADERS = [
            "Content-Type: application/json",
            "Authorization: key=" . config('notification.firebase.apiServerKey')
        ];
        $POST_DATA = json_encode($ReadyData);
        $CH        = curl_init();
        curl_setopt( $CH, CURLOPT_HTTPHEADER     , $HEADERS );
        curl_setopt( $CH, CURLOPT_URL            , 'https://fcm.googleapis.com/fcm/send');
        curl_setopt( $CH, CURLOPT_SSL_VERIFYHOST , 0 );
        curl_setopt( $CH, CURLOPT_SSL_VERIFYPEER , 0 );
        curl_setopt( $CH, CURLOPT_RETURNTRANSFER , true );
        curl_setopt( $CH, CURLOPT_POSTFIELDS     , $POST_DATA );
        $RESPONSE = curl_exec($CH);
        $ERROR    = curl_error($CH);
        curl_close($CH);
        // [أمان 2026-09-14] تعطيل تفريغ debug كان يسرّب توكنات الأجهزة: file_put_contents(public_path('firebase.txt'), json_encode($RESPONSE, JSON_PRETTY_PRINT));
        // [أمان 2026-09-14] تعطيل تفريغ debug كان يسرّب توكنات الأجهزة: file_put_contents(public_path('firebase-sent-data.txt'), json_encode($AdditionalData, JSON_PRETTY_PRINT));
        // dd($RESPONSE);
        $res = json_decode($RESPONSE, true);
        // [أمان 2026-09-14] تعطيل تفريغ debug كان يسرّب توكنات الأجهزة: file_put_contents(public_path('firebase-result.txt'), json_encode($RESPONSE, JSON_PRETTY_PRINT));

        return $res;
    }
}
