<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FlagCountry extends Model
{
    public $timestamps = false;
    
    protected $table = 'countries';
    
}
