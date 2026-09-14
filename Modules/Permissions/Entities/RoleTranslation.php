<?php

namespace Modules\Permissions\Entities;

use Illuminate\Database\Eloquent\Model;

class RoleTranslation extends Model
{
    protected $table = 'perms_role_translations';

    protected $guarded = [];

    public $timestamps = false;
}
