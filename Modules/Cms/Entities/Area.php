<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Tag;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Modules\Cms\Entities\Traits\Disabable;
use Modules\Cms\Entities\Attributes;

class Area extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes;
    public $timestamps = false;
    protected $fillable = [
        'city_id',
        'native_name',
        'zip_code',
        'lat',
        'lng',
    ];
    public $translationModel = 'Modules\Cms\Entities\AreaTranslation';
    protected $table = 'cms_areas';
    public $translatedAttributes = [
        'name',
        'image',
        'about',
        'short_description',
        'keywords',
        'details',
        'description',
    ];

    public function City()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'cms_taggables');
    }
    public function formAjaxArray($is_selected = true)
    {
        return [
            'id'        => $this->id,
            'text'      => "{$this->translateOrFirst(app()->getLocale())->name}",
            'selected'  => $is_selected,
        ];
    }

    public function customFields()
    {
        return $this->morphMany(Attributes::class, 'content');
    }
}
