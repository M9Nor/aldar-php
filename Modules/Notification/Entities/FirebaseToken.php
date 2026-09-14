<?php

namespace Modules\Notification\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use Modules\Notification\Entities\Traits\TranslatableHelper;
use Modules\Notification\Entities\Traits\Disabable;

class FirebaseToken extends Model
{
    protected $table    = 'notif_tokens';

    public $timestamps  = true;

    protected $fillable = [
        'user_id',
        'token',
        'last_activity',
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
