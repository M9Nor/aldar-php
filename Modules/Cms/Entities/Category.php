<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\Traits\Disabable;
use Modules\Cms\Entities\Content;
use Illuminate\Support\Str;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\Tag;
use Modules\Backend\Entities\PayingMethod;

class Category extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes, Disabable;
    protected $table = 'cms_categories';
    protected $guarded = [];
    public $translationModel = 'Modules\Cms\Entities\CategoryTranslation';
    protected $fillable = [
        'parent_id',
        'slug',
        'type',
        'status',
        'sort_order',
    ];
    public $translatedAttributes = [
        'title',
        'brief',
        'description',
        'image',
        'seo_description',
        'keywords',
    ];
    protected static $imageOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
            '180x180',
            '400x400',
            '1000x750',
            '1000x1000',
            '130x85',
        ],
    ];

    protected static $typeList = [
        // 'news' => [ // Categories.
        //     'icon'          => 'cms.svgs.news',
        //     'aside_menu'    => true, // Appears on aside menu?
        //     'singular'      => 'backend::cruds.categories.news.singular',
        //     'plural'        => 'backend::cruds.categories.news.plural',
        //     'fields'        => [
        //         'slug'          => ['required' => true],
        //         'image'         => ['required' => false],
        //         'title'         => ['required' => true],
        //         'brief'         => [],
        //         'description'   => [],
        //         'sort_order'    => [],
        //         'tags'          => [],
        //         'keywords'      => [],
        //         // 'parent'        => []
        //     ]
        // ],
        'articles' => [ // Categories.
            'icon'          => 'cms.svgs.articles',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.articles.singular',
            'plural'        => 'backend::cruds.categories.articles.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'sort_order'    => [],
                'seo_description'=> [],
                'tags'          => [],
                'keywords'      => [],
                // 'parent'        => []
            ]
        ],
        'agents' => [ // Categories.
            'icon'          => 'cms.svgs.agents',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.agents.singular',
            'plural'        => 'backend::cruds.categories.agents.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'sort_order'    => [],
                'seo_description'=> [],
                // 'tags'          => [],
                'keywords'      => [],
                // 'parent'        => []
            ]
        ],
        'faqs' => [ // Categories.
            'icon'          => 'cms.svgs.faqs',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.faqs.singular',
            'plural'        => 'backend::cruds.categories.faqs.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'title'         => ['required' => true],
                'brief'         => ['required' => true],
                'tags'          => [],
                'keywords'      => [],
                'sort_order'    => []
            ]
        ],
        'filters' => [ // Categories.
            'icon'          => 'cms.svgs.faqs',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.filters.singular',
            'plural'        => 'backend::cruds.categories.filters.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'parent'        => [],
                'title'         => ['required' => true],
                'brief'         => ['required' => true],
                'tags'          => [],
                'keywords'      => [],
                'sort_order'    => []
            ]
        ],
        'playlist_videos' => [ // Channel playlists. Categories.
            'icon'          => 'cms.svgs.playlists',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.playlists.singular',
            'plural'        => 'backend::cruds.categories.playlists.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required' => false,
                    'dimensions'        => [
                        'preview' => '500x375',
                        '85x85',
                        '250x187',
                        '1000x750',
                    ]
                ],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'tags'          => [],
                'keywords'      => [],
                'seo_description'=> [],
                'sort_order'    => []
            ]
        ],
        'contracts' => [ // Channel contracts. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.contracts.singular',
            'plural'        => 'backend::cruds.categories.contracts.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'tags'          => [],
                'keywords'      => [],
                'sort_order'    => []
            ]
        ],
        'property_classifications' => [ // Channel playlists. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.property_classifications.singular',
            'plural'        => 'backend::cruds.categories.property_classifications.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'tags'          => [],
                'keywords'      => [],
                'sort_order'    => [],
                'parent'        => []
            ]
        ],

        'opportunity_classifications' => [ // Channel playlists. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.property_classifications.singular',
            'plural'        => 'backend::cruds.categories.property_classifications.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'tags'          => [],
                'keywords'      => [],
                'sort_order'    => [],
                'parent'        => []
            ]
        ],
        
        'property_status' => [ // Channel playlists. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.property_status.singular',
            'plural'        => 'backend::cruds.categories.property_status.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'sort_order'    => [],
                'seo_description'=> [],
                'keywords'      => [],
                'tags'          => [],
                'icon_image'    => ['required' => false],
            ]
        ],
        'property_features' => [ // Channel playlists. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.property_features.singular',
            'plural'        => 'backend::cruds.categories.property_features.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'sort_order'    => [],
                'seo_description'=> [],
                'keywords'      => [],
                'tags'          => [],
                'icon_image'    => ['required' => false],
            ]
        ],
        'facilities' => [ // Channel playlists. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.facilities.singular',
            'plural'        => 'backend::cruds.categories.facilities.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'sort_order'    => [],
                'seo_description'=> [],
                'keywords'      => [],
                'tags'          => [],
                'icon_image'    => ['required' => false],
            ]
        ],
        'payments' => [ // Channel playlists. Categories.
            'icon'          => '',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.categories.payments.singular',
            'plural'        => 'backend::cruds.categories.payments.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => ['required' => false],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'tags'          => [],
                'keywords'      => [],
                'sort_order'    => [],
                'parent'        => []
            ]
        ],
        // new
        // 'filters_cat' => [ // Channel playlists. Categories.
        //     'icon'          => '',
        //     'aside_menu'    => true, // Appears on aside menu?
        //     'singular'      => 'backend::cruds.categories.payments.singular',
        //     'plural'        => 'backend::cruds.categories.payments.plural',
        //     'fields'        => [
        //         'slug'          => ['required' => true],
        //         'image'         => ['required' => false],
        //         'title'         => ['required' => true],
        //         'brief'         => [],
        //         'description'   => [],
        //         'tags'          => [],
        //         'keywords'      => [],
        //         'sort_order'    => [],
        //         'parent'        => []
        //     ]
        // ],
    ];

    /**
     * Returns a collection of available types in the content model.
     */
    public static function types()
    {
        return collect(static::$typeList);
    }

    /**
     * Checks if a specific type exists in the type collection.
     */
    public static function typeExists($type)
    {
        return self::types()->contains(function($item, $key) use ($type) {
            return $key == $type;
        });
    }

    public static function getTypeInfo($type)
    {
        return self::types()->first(function($item, $key) use ($type) {
            return $key == $type;
        });
    }
    public function payingMethods(){
        return $this->hasMany(PayingMethod::class,'project_type_id', 'id');
    }
    public function contents()
    {
        return $this->morphedByMany(Content::class, 'categorizable', 'cms_categorizables')->select(['*', \DB::raw('IF(`sort_order` IS NOT NULL, `sort_order`, 1000000) `sort_order`')])->orderBy('sort_order','ASC');
    }
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'categorizable', 'cms_categorizables');
    }

    /**
     * Returns an array of a content type's field list.
     */
    public static function getTypeFields($type)
    {
        $typeInfo = self::getTypeInfo($type);

        if(! isset($typeInfo['fields'])) throw new \Exception("Index 'fields' does not exists for '{$type}' type.", 404);

        return $typeInfo['fields'];
    }

    /**
     * Returns an array of a content type's field names list.
     */
    public static function getTypeFieldNames($type)
    {
        return array_keys(self::getTypeFields($type));
    }

    public static function typeAbort($type)
    {
        if($type)
        {

            if(! self::typeExists($type))
            {
                abort(404);
            }
        }
    }

    /**
     * Checks if a content type has a specific field.
     */
    public static function typeHasField($type, $field)
    {
        return in_array($field, self::getTypeFieldNames($type));
    }

    public static function getTypeTitle($type, $plural = true)
    {
        $typeInfo = self::getTypeInfo($type);

        if($plural)
        {
            if(! isset($typeInfo['plural'])) throw new \Exception("Index 'plural' does not exists for '{$type}' type.", 404);
            return __($typeInfo['plural']);
        }
        else
        {
            if(! isset($typeInfo['singular'])) throw new \Exception("Index 'singular' does not exists for '{$type}' type.", 404);
            return __($typeInfo['singular']);

        }
    }

    public static function getFieldLabel($type, $field, $params = [])
    {
        if(self::typeHasField($type, $field))
        {
            $fieldCustomLocalization = self::getTypeFields($type)[$field];

            if(!empty($fieldCustomLocalization) && isset($fieldCustomLocalization['label']))
            {
                return __($fieldCustomLocalization['label'], $params);
            }
            else
            {
                return __('cms::cruds.categories.'.$field.'.label', $params);
            }
        }

        throw new \Exception("'{$type}' type does not have '{$field}' field.", 404);
    }

    public static function getFieldPlaceholder($type, $field, $params = [])
    {
        if(self::typeHasField($type, $field))
        {
            $fieldCustomLocalization = self::getTypeFields($type)[$field];

            if(!empty($fieldCustomLocalization) && isset($fieldCustomLocalization['placeholder']))
            {
                return __($fieldCustomLocalization['placeholder'], $params);
            }
            else
            {
                return __('cms::cruds.categories.'.$field.'.placeholder', $params);
            }
        }

        throw new \Exception("'{$type}' type does not have '{$field}' field.", 404);
    }

    public static function getFieldHelp($type, $field, $params = [])
    {
        if(static::typeHasField($type, $field))
        {
            $fieldCustomLocalization = self::getTypeFields($type)[$field];

            if(!empty($fieldCustomLocalization) && isset($fieldCustomLocalization['help']))
            {
                return __($fieldCustomLocalization['help'], $params);
            }
            else
            {
                return __('cms::cruds.categories.'.$field.'.help', $params);
            }
        }

        throw new \Exception("'{$type}' type does not have '{$field}' field.", 404);
    }

    public static function imageDimensionsByType($type){
        if(static::typeHasField($type, 'image'))
        {
            $imageOptions = self::getTypeFields($type)['image'];

            if(!empty($imageOptions) && isset($imageOptions['dimensions']) &&  !empty($imageOptions['dimensions']))
            {
                return collect($imageOptions['dimensions']);
            }
            else
            {
                return collect(self::$imageOptions['dimensions']);
            }
        }

        return collect([]);
    }

    public static function isFieldRequired($type, $field){
        if(static::typeHasField($type, $field))
        {
            $fieldOptions = self::getTypeFields($type)[$field];

            if(!empty($fieldOptions) && isset($fieldOptions['required']))
            {
                return $fieldOptions['required'];
            }
        }

        return false;
    }

    public function getTranslatedImage($size = '85x85', $locale = null){
        if (is_null($locale)) {
            $locale = app()->getLocale();
        }
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->translateOrFirst($locale)->image, 'http')){
            return $this->translateOrFirst($locale)->image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if($this->type != 'contents')
        {
            if(! self::imageDimensionsByType($this->type)->contains($size)){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        else
        {
            if(! self::imageDimensions()->contains($size)){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        return ($this->translateOrFirst($locale)->image)
        ? route('image', ['size' => $size, 'path' => $this->translateOrFirst($locale)->image])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/'.$this->type.'.png'
        ]);
    }

    public static function getImage($model, $size = '85x85', $type = 'contents'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($model->image, 'http')){
            return $model->image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if($type != 'contents')
        {
            if(! self::imageDimensionsByType($type)->contains($size)){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        else
        {
            if(! self::imageDimensions()->contains($size)){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        return ($model->image)
        ? route('image', ['size' => $size, 'path' => $model->image])
        : route('image', [
            'size' => $size,
            // 'path' => 'defaults/'.$type.'.png'
            'path' => 'defaults/categories.png'
        ]);
    }
    public static function getIconImage($model, $size = '85x85', $type = 'contents'){

        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($model->icon_image, 'http')){
            return $model->icon_image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if($type != 'contents')
        {
            if(! self::imageDimensionsByType($type)->contains($size)){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        else
        {
            if(! self::imageDimensions()->contains($size)){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        return ($model->icon_image)
        ? route('image', ['size' => $size, 'path' => $model->icon_image])
        : route('image', [
            'size' => $size,
            // 'path' => 'defaults/'.$type.'.png'
            'path' => 'defaults/categories.png'
        ]);
    }
    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }
    public function AllCategoryParents($category,$result = [],$collection = null){
        $parent_category = is_null($collection) ? Category::find($category->parent_id) : $collection->where('id',$category->parent_id)->first();
        if(!is_null($parent_category)){
            $result[] = $parent_category;
            $result = array_merge(Category::AllCategoryParents($parent_category,[],$collection),$result);
        }
        return $result;
    }
    public function formAjaxArray($is_selected = true){
        if($this->type == 'istanbul_real_estate' || $this->type == 'turkey_real_estate'){
            return [
                'id'        =>  $this->id,
                'text'      => $this->translateOrFirst(app()->getLocale())->title,
                'sub_text'  => 'footer',
                'selected'  =>  $is_selected,
            ];
        }else{
            return [
                'id'        =>  $this->id,
                'text'      => $this->translateOrFirst(app()->getLocale())->title,
                'selected'  =>  $is_selected,
            ];
        }

    }
    public function getAllChildren($children = [])
    {
        $children = collect($children);
        $this->children->map(function($child) use (&$children) {
            $children->push($child);
            $children = $child->getAllChildren($children);

            return $child;
        });

        return collect($children);
    }
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
        ->when(session('loadContent', false), function($query) {
            $query->with('contents');
        })
        ->with('children');
    }
    public static function GetAllChildsIds($category, $result = []){
        foreach ($category->ChildsCategories as $key => $value) {
            $result[] = $value->id;
            $result   = static::GetAllChildsIds($value, $result);
        }
        return $result;
    }
    public static function GetAllChilds($category, $result = []){
        foreach ($category->ChildsCategories as $key => $value) {
            $result[] = $value;
            $result   = static::GetAllChilds($value, $result);
        }
        return $result;
    }
    public function ChildsCategoriesWithTrashed(){ // get onlty first level of parent
        return $this->hasMany( static::class, 'parent_id', 'id' )->withTrashed();
    }
    public function parent(){ // get onlty first level of parent
        return $this->belongsTo( static::class, 'parent_id', 'id' )->withTrashed();
    }
    public function GetAllChildsWithTrashed($category, $result = []){
        foreach ($category->ChildsCategoriesWithTrashed as $key => $value) {
            $result[] = $value;
            $result   = static::GetAllChildsWithTrashed($value, $result);
        }
        return $result;
    }
    public function ChildsCategories(){ // get onlty first
        return $this->hasMany( static::class, 'parent_id', 'id' );
    }
    public function scopeCategoryType($query,$type = []){
        return $query->when(!empty($type), function($q) use ($type) {
            $q->whereIn('type', $type);
        });
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'cms_taggables');
    }
}
