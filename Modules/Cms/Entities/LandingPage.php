<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Modules\Cms\Entities\Traits\Disabable;
use Illuminate\Support\Str;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\Timeline;

class LandingPage extends Model
{
    use Translatable,TranslatableHelper,SoftDeletes;
    // public $timestamps  = false;
    protected $table = 'landing_pages';
    protected $fillable = [
        // 'country_id',
        // 'native_name',
        // 'zip_code',
        // 'lat',
        // 'lng',
        // 'link',
    ];

    protected static $imageOptions = [
        'dimensions' => [
            'preview' => '500x375',
            '1000x750',
            '730x350',
            '350x350',
        ],
    ];
    public $translationModel = 'Modules\Cms\Entities\LandingPageTranslation';
    public $translatedAttributes = [
        'meta_title',
        'meta_keywords',
        'meta_desc',
        'meta_img',
        'header_logo',
        'header_background',
        'header_h1',
        'header_h2',
        'header_h1_above',
        'header_h2_above',
        'header_h1_under',
        'header_h2_under',
        'subject_text_h2',
        'subject_text_desc',
        'videos_title',
        'subject_text2_h2',
        'subject_text2_h3',
        'subject_text2_desc',
        'timeline_title',
    ];
    
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

    public function timeline(){
        return $this->hasMany(Timeline::class,'landing_id','id');
    }
}
