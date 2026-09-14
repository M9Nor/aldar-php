<?php

namespace Modules\Notification\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use Modules\Notification\Entities\Traits\TranslatableHelper;
use Modules\Notification\Entities\Traits\Disabable;

class FirebaseNotificationReceiverData extends Model
{
    protected $table    = 'notif_notification_receiver_data';

    public $timestamps  = false;

    protected $fillable = [
        'notification_id',
        'receiver_id',
        'x_key',
        'x_val',
    ];
}
