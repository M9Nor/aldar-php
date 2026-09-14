<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Modules\Cms\Entities\Traits\Disabable;

class Country extends Model
{
    use Translatable, TranslatableHelper,SoftDeletes;
    public $timestamps = false;
    protected $table = 'cms_countries';
    protected $fillable = [
        'dial_code',
        'lat',
        'lng',
    ];
    public $translationModel = 'Modules\Cms\Entities\CountryTranslation';
    public $translatedAttributes = [
        'name',
        'description',
    ];
    public function formAjaxArray($is_selected = true)
    {
        return [
            'id'        => $this->id,
            'text'      => "{$this->translateOrFirst(app()->getLocale())->name}",
            'selected'  => $is_selected,
        ];
    }
}
