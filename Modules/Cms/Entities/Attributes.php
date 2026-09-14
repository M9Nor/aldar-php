<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attributes extends Model
{
    
    protected $table = 'cms_attributes';
    protected $fillable = [
        'content_id',
        'type',
        'key',
        'value'
    ];
}
