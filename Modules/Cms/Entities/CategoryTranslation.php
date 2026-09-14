<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryTranslation extends Model
{
    protected $table = 'cms_category_translations';
    protected $guarded = [];
    public $timestamps = false;
}
