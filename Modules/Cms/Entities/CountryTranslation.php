<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    protected $table = 'cms_country_translations';

    protected $fillable = [
        'country_id',
        'locale',
        'name',
    ];

    public $timestamps = false;
}
