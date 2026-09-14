<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Modules\Cms\Entities\Traits\Disabable;
use Illuminate\Support\Str;
use Modules\Cms\Entities\LandingPage;
class Timeline extends Model
{
    // use Translatable,TranslatableHelper,SoftDeletes;
    // public $timestamps  = false;

    protected $table = 'timeline';
    protected $fillable = [
        'landing_id',
        'language',
        'title',
        'date',
        'link',
        'icon',
        'image',
        'sort_order',
        'description',
        'created_at',
        'updated_at',
    ];

    public function ladndingPage(){
        return $this->hasOne(LandingPage::class,'id','landing_id');
    }

    public static function getIconImage($model, $size = '85x85'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($model->icon, 'http')){
            return $model->icon;
        }
        
        if(! self::imageDimensions()->contains($size) && $size != 'original'){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($model->icon)
        ? route('image', ['size' => $size, 'path' => $model->icon])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/categories.png'
        ]);
    }

    public static function getImage($model, $size = '85x85'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($model->image, 'http')){
            return $model->image;
        }
        
        if(! self::imageDimensions()->contains($size) && $size != 'original'){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($model->image)
        ? route('image', ['size' => $size, 'path' => $model->image])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/categories.png'
        ]);
    }

    

    protected static $imageOptions = [
        'dimensions' => [
            '1920x1280',
            '1000x750',
            '698x500',
            '500x375',
            '100x100'
        ],
    ];

    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }

}
