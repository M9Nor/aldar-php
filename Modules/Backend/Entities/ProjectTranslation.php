<?php

namespace Modules\Backend\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectTranslation extends Model
{
    protected $table    = 'be_projects_translations';
    protected $guarded  = [];
    public $timestamps  = false;
    protected static $imageOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
            '698x500',
            '349x250',
            '400X400',
            '500x375'
        ],
    ];
    public function getImage($size = '85x85')
    {
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->image, 'http')){
            return $this->image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if(! self::imageDimensions()->contains($size) && $size != 'original' ){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->image)
        ? route('image', ['size' => $size, 'path' => $this->image])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }
    public static function imageDimensions()
    {
        return collect(self::$imageOptions['dimensions']);
    }
    public function getImageForVideo1($size = '85x85')
    {
        if(Str::startsWith($this->project_image1, 'http')){
            return $this->project_image1;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->project_image1)
        ? route('image', ['size' => $size, 'path' => $this->project_image1])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }
    public function getImageForVideo2($size = '85x85')
    {
        if(Str::startsWith($this->project_image2, 'http')){
            return $this->project_image2;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->project_image2)
        ? route('image', ['size' => $size, 'path' => $this->project_image2])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }

}
