<?php

namespace Modules\Permissions\Entities;

use Illuminate\Database\Eloquent\Model;

class AbilityTranslation extends Model
{
    protected $table = 'perms_ability_translations';

    protected $guarded = [];

    public $timestamps = false;
}
