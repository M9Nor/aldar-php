<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConfigTranslation extends Model
{
    protected $table    = 'cms_config_translations';
    public $timestamps  = false;
    protected $fillable = [
        
    ];
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
        if(Str::startsWith($this->image, 'http')){
            return $this->image;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->image)
        ? route('image', ['size' => $size, 'path' => $this->image])
        : route('image', ['size' => $size, 'path' => 'defaults/config.png']);
    }
    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }
}
