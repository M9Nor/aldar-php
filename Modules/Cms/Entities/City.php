<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Modules\Cms\Entities\Traits\Disabable;
use Modules\Cms\Entities\Country;
use Illuminate\Support\Str;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\Attributes;

class City extends Model
{
    use Translatable,TranslatableHelper,SoftDeletes;
    public $timestamps = false;
    protected $fillable = [
        'country_id',
        'native_name',
        'zip_code',
        'lat',
        'lng',
        'link',
    ];

    protected static $imageOptions = [
        'dimensions' => [
            'preview' => '500x375',
            '1000x750',
            '730x350',
            '350x350',
        ],
    ];
    public $translationModel = 'Modules\Cms\Entities\CityTranslation';
    protected $table = 'cms_cities';
    public $translatedAttributes = [
        'name',
        'description',
        'image',
    ];
    public function Country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public function formAjaxArray($is_selected = true)
    {
        return [
            'id'        => $this->id,
            'text'      => "{$this->translateOrFirst(app()->getLocale())->name}",
            'selected'  => $is_selected,
        ];
    }

    public function projects(){
        return $this->hasMany(Project::class, 'city_id', 'id');
    }

    public function getTranslatedImage($size = '85x85', $locale = null){
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->translateOrFirst($locale)->image, 'http')){
            return $this->translateOrFirst($locale)->image;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }

        return ($this->translateOrFirst($locale)->image)
        ? route('image', ['size' => $size, 'path' => $this->translateOrFirst($locale)->image])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/'.$this->type.'.png'
        ]);
    }

    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }

    public function customFields()
    {
        return $this->morphMany(Attributes::class, 'content');
    }
}
