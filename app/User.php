<?php

namespace App;

use Modules\Permissions\Entities\Traits\PermissionsUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Modules\Cms\Entities\Traits\CmsUser;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, CmsUser, PermissionsUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to the model.
     *
     * @var array
     */
    protected $appends = [
        'full_name'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Exclude the ROOT users from all querys.
        // static::addGlobalScope('notRoot', function (Builder $builder) {
        //     $builder->where('type', '!=', 'ROOT');
        // });
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            // 'BASE_USER_ID' => $this->id
        ];
    }

    public function getFullNameAttribute()
    {
        if(!empty($this->first_name))
            return $this->first_name . (!empty($this->last_name) ? ' ' . $this->last_name : '');

        return $this->username;
    }

    public function toArray()
    {
        return [
            'id'            => $this->id ?? 0,
            'full_name'     => $this->full_name,
            'username'      => $this->username,
            'email'         => $this->email,
            'online'        => $this->online,
            'status'        => $this->status,
            'image'         => $this->getImage('150x150'),
            'roles_array'   => $this->roles->map(function($role) {
                $item['color'] = $role->color;
                $item['title'] = $role->translateOrFirst()->title;

                return $item;
            }),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'deleted_at'    => $this->deleted_at,
        ];
    }

    public function formAjaxArray($is_selected = true){
        return [
            'id'        => $this->id,
            'text'      => "{$this->username}",
            'image'     => $this->image ,
            'selected'  => $is_selected,
        ];
    }
}
