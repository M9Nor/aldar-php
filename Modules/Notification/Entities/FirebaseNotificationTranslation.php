<?php

namespace Modules\Notification\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use Modules\Notification\Entities\Traits\TranslatableHelper;
use Modules\Notification\Entities\Traits\Disabable;

class FirebaseNotificationTranslation extends Model
{
    protected $table    = 'notif_notification_translations';

    public $timestamps  = false;

    protected $fillable = [
        'firebase_notification_id',
        'locale',
        'title',
        'body',
        'icon',
        'image',
        'sound',
    ];
}
