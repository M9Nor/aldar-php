<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;

class UserTypeTranslation extends Model
{
    protected $table = 'user_type_translations';

    protected $guarded = [];

    public $timestamps = false;
}
