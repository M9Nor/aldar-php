<?php

namespace Modules\Backend\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\Helpers;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Modules\Cms\Entities\Traits\Disabable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\Tag;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Country;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\Area;
use Modules\Cms\Entities\Attachment;
use Modules\Backend\Entities\PayingMethod;
use Modules\Backend\Entities\Price;
use Illuminate\Support\Str;

class Project extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes, Disabable, Helpers;
    /**
     * User Statuses
     */
    protected static $statuses = [
        'UNACTIVE' => [
            'code'          => 'UNACTIVE',
            'option_class'  => 'm--font-warning',
            'label'         => 'backend::projects.statuses.UNACTIVE.label'
        ],

        'ACTIVE' => [
            'code'          => 'ACTIVE',
            'option_class'  => 'm--font-success',
            'label'         => 'backend::projects.statuses.ACTIVE.label'
        ],
    ];
    protected $table = 'be_projects';

    public $translationModel = 'Modules\Backend\Entities\ProjectTranslation';

    protected $fillable = [
        'code',
        'content_id',
        'status',
        'video',
        'link',
        'location',
        'delivery_date',
        'establishment_date',
        'views',
        'likes',
        'country_id',
        'city_id',
        'deleted_at',
        'roi',
    ];
    public $translatedAttributes = [
        'project_id',
        'locale',
        'title',
        'description',
        'details',
        'about',
        'brief',
        'image',
        'seo_description',
        'trans_keywords',
        'delivery_date',
        'video',
        'video2',
        'project_image1',
        'project_image2',
        'tab_title',
        'second_title',
        'landing_page_title',
        'landing_page_desc',
    ];

    protected static $imageOptions = [
        'dimensions' => [
            '1920x1280',
            '1000x750',
            '698x500',
            '500x375',
            '400x400',
            '1200x848',
            '1920x600'
        ],
    ];
    public function seoDescription(){
        if(!empty($this->translateOrFirst()->seo_description)){
            $seo_description = $this->translateOrFirst()->seo_description;
        }else{
            $seo_description = $this->translateOrFirst()->brief;
        }
        return $seo_description;
    }
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
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }
    public function getImageByCustomField($field,$size = '85x85'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->{$field}, 'http')){
            return $this->{$field};
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }
        return ($this->{$field})
        ? route('image', ['size' => $size, 'path' => $this->{$field}])
        : route('image', ['size' => $size, 'path' => 'defaults/projects.png']);
    }

    public function getTranslatedImage($size = '85x85', $locale = null){
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith(empty($image = $this->translateOrFirst($locale)->image) ? (!is_null($firstTrasnlationWithImage = $this->translations->where('image', '!=', null)->first()) ? $firstTrasnlationWithImage->image : '') : $this->translateOrFirst($locale)->image, 'http')){
            return empty($image = $this->translateOrFirst($locale)->image) ? (!is_null($firstTrasnlationWithImage = $this->translations->where('image', '!=', null)->first()) ? $firstTrasnlationWithImage->image : '') : $this->translateOrFirst($locale)->image;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }

        return (empty($image = $this->translateOrFirst($locale)->image) ? (!is_null($firstTrasnlationWithImage = $this->translations->where('image', '!=', null)->first()) ? $firstTrasnlationWithImage->image : '') : $this->translateOrFirst($locale)->image)
        ? route('image', ['size' => $size, 'path' => empty($image = $this->translateOrFirst($locale)->image) ? (!is_null($firstTrasnlationWithImage = $this->translations->where('image', '!=', null)->first()) ? $firstTrasnlationWithImage->image : '') : $this->translateOrFirst($locale)->image])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/attachments.png'
        ]);
    }

    public function getTranslatedVideoImage1($size = '85x85', $locale = null){
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->translateOrFirst($locale)->project_image1, 'http')){
            return $this->translateOrFirst($locale)->project_image1;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }

        return ($this->translateOrFirst($locale)->project_image1)
        ? route('image', ['size' => $size, 'path' => $this->translateOrFirst($locale)->project_image1])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/'.$this->type.'.png'
        ]);
    }


    public function getTranslatedVideoImage3($size = '85x85', $locale = null){
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->translateOrFirst($locale)->project_image2, 'http')){
            return $this->translateOrFirst($locale)->project_image2;
        }
        if(! self::imageDimensions()->contains($size)){
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }

        return ($this->translateOrFirst($locale)->project_image2)
        ? route('image', ['size' => $size, 'path' => $this->translateOrFirst($locale)->project_image2])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/'.$this->type.'.png'
        ]);
    }

    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }

    public static function collectStatuses()
    {
        return collect(static::$statuses);
    }

    public function areas(){
        return $this->belongsToMany('Modules\Cms\Entities\Area','be_projects_areas','project_id','area_id');
    }
    public function contents(){
        return $this->belongsToMany('Modules\Cms\Entities\Content','be_projects_contents','project_id','content_id');
        // $relation = $this->belongsToMany('Modules\Cms\Entities\Content','projects_contents','project_id','content_id');
        // if(!empty($type)){
        //     $relation = $relation->whereIn('type',$type);
        // }
        // return $relation;
    }
    public function categories(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->withPivot('options');
        // $relation =  $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables');
        // if(!empty($type)){
        //     $relation = $relation->whereIn('type',$type);
        // }
        // return $relation;
    }

    public function allCategories(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->withPivot('options');
    }

    public function contracts(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'contracts');
    }

    public function propertyClassifications(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'property_classifications');
    }

    public function opportunityClassifications(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'opportunity_classifications');
    }


    public function propertyFeatures(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'property_features')->withPivot('options');
    }

    public function propertyStatues(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'property_status');
    }

    public function facilities(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'facilities');
    }

    public function paymentsCategories(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'payments');
    }

    public function propertyType(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'property_classifications');
    }

    public function opportunityType(){
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables')->where('type', 'opportunity_classifications');
    }

    
    public function tags(){
        return $this->morphToMany(Tag::class, 'taggable', 'cms_taggables');
    }
    public function country(){
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function area(){
        return $this->hasOne(Area::class, 'id', 'area_id');
    }

    public function attachments(){
        return $this->morphMany(Attachment::class,'attachable');
    }

    public function payments(){
        return $this->hasMany(PayingMethod::class,'project_id','id');
    }

    public function prices(){
        return $this->hasMany(Price::class,'project_id','id');
    }

    public function priceByLowestPrice(){
        return $this->hasOne(Price::class,'project_id','id')->whereRaw('id', 'min_max_price.price_id');
    }
}
