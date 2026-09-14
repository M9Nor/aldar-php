<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LandingPageTranslation extends Model
{
    protected $table = 'landing_page_translations';
    protected $guarded = [];
    public $timestamps = false;

    
    protected static $imageOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
            '400x200',
            '400x400',
            '1000x1000',
        ],
    ];
    public function getMetaImage($size = '85x85')
    {
        if(Str::startsWith($this->meta_img, 'http')){
            return $this->meta_img;
        }
        if(! self::imageDimensions()->contains($size) && $size != 'original' ){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->meta_img)
        ? route('image', ['size' => $size, 'path' => $this->meta_img])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }
    public function headerLogo($size = '85x85')
    {
        if(Str::startsWith($this->header_logo, 'http')){
            return $this->header_logo;
        }
        if(! self::imageDimensions()->contains($size) && $size != 'original'){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->header_logo)
        ? route('image', ['size' => $size, 'path' => $this->header_logo])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }
    public function headerBackground($size = '85x85')
    {
        if(Str::startsWith($this->header_background, 'http')){
            return $this->header_background;
        }
        if(! self::imageDimensions()->contains($size) && $size != 'original'){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->header_background)
        ? route('image', ['size' => $size, 'path' => $this->header_background])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }
    public static function imageDimensions()
    {
        return collect(self::$imageOptions['dimensions']);
    }
}
