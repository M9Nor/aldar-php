<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CityTranslation extends Model
{
    protected $table = 'cms_city_translations';

    protected $fillable = [
        'city_id',
        'locale',
        'name',
        'image',
        'description',
    ];
    public $timestamps = false;
    protected static $imageOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
            '400x400',
            '1000x1000',
        ],
    ];
    public function getImage($size = '85x85'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->image, 'http')){
            return $this->image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->image)
        ? route('image', ['size' => $size, 'path' => $this->image])
        : route('image', ['size' => $size, 'path' => 'defaults/cities.png']);
    }
    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }
}
