<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\Helpers;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\Traits\Disabable;
use Modules\Cms\Entities\Attributes;
use Modules\Cms\Entities\Tag;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\ExternalAttachments;
use Illuminate\Support\Str;

class Content extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes, Disabable,Helpers;
    protected $table = 'cms_contents';
    protected $guarded = [];
    public $translationModel = 'Modules\Cms\Entities\ContentTranslation';
    protected $fillable = [
        'slug',
        'type',
        'cover',
        'disabled_at',
        'deleted_at',
        'currency_symbol',
        'currency_value',
        'currency_icon',
        'currency_code',
    ];
    public $translatedAttributes = [
        'title',
        'brief',
        'image',
        'description',
        'about',
        'keywords',
        'seo_description',
        'link_trans',
    ];
    public static $contentWithSpecificDefaults = [
        'pages'
    ];
    protected static $typeList = [
        'sliders' => [ // Contents.
            'icon'          => 'cms.svgs.sliders',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.sliders.singular',
            'plural'        => 'backend::cruds.contents.sliders.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'          => false,
                    'dimensions' => [
                        'preview' => '600x300',
                        '85x85',
                        '1920x960',
                    ]
                ],
                'title'         => [
                    'label'         => 'backend::cruds.contents.sliders.title.label',
                ],
                'sort_order'    => [],
                'link'          => [],
            ],
        ],
        'stories' => [ // Content with attachments.
            'icon'          => 'cms.svgs.stories',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.stories.singular',
            'plural'        => 'backend::cruds.contents.stories.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                // Leave field array empty if you don't wish to override the existing localization keys.
                'image'         => [
                    'required' => true,
                    'dimensions'        => [
                        'preview' => '400x400',
                        '85x85',
                        '180x180',
                        '640x1066',
                    ]
                ],
                'title'         => ['required' => true],
                'sort_order'    => [],
                'attachments'   => [
                    'required'          => false,
                    'validation_rules'  => 'bail|required|image|max:2048|mimes:jpeg,jpg,png|dimensions:min_width=250,min_height=500,max_width=1000,max_height=2000'
                ]
            ]
        ],
        // 'news' => [ // Categorized contents.
        //     'icon'          => 'cms.svgs.news',
        //     'aside_menu'    => false, // Appears on aside menu?
        //     'attachments'   => false,
        //     'singular'      => 'backend::cruds.contents.news.singular',
        //     'plural'        => 'backend::cruds.contents.news.plural',
        //     'fields'        => [
        //         'slug'          => ['required' => true],
        //         'image'         => [
        //             'required'      => false,
        //             'dimensions'    => [
        //                 'preview' => '425x250',
        //                 '85x85',
        //                 '90x80',
        //                 '341x218',
        //                 '735x403',
        //                 '850x500',
        //             ]
        //         ],
        //         'categories'        => ['required' => true],
        //         'title'             => [],
        //         'brief'             => [],
        //         'description'       => [],
        //         'sort_order'        => [],
        //         'tags'              => ['required' => false],
        //         'keywords'          => ['required' => false],
        //         'seo_description'   => ['required' => false],
        //     ]
        // ],
        'articles' => [ // Categorized contents.
            'icon'          => 'cms.svgs.articles',
            'aside_menu'    => false, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.articles.singular',
            'plural'        => 'backend::cruds.contents.articles.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'        => [
                        'preview' => '500x375',
                        '85x85',
                        '1000x750',
                    ]
                ],
                'categories'    => ['required' => true],
                'title'         => [],
                'brief'         => [],
                'description'   => [],
                'sort_order'    => [],
                'tags'              => ['required' => false],
                'about'             => ['required' => false],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
                'views'             => ['required' => false],
            ]
        ],
        'faqs' => [ // Categorized contents.
            'icon'          => 'cms.svgs.faqs',
            'aside_menu'    => false, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.faqs.singular',
            'plural'        => 'backend::cruds.contents.faqs.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'categories'    => ['required' => true],
                // 'title' refers to the question.
                'title'         => ['required' => true],
                // 'description' refers to the question answer.
                'description'   => ['required' => true],
                'sort_order'    => [],
                'tags'              => ['required' => false],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
            ]
        ],
        'playlist_videos' => [ // Channel playlist videos. Contents.
            'icon'          => 'cms.svgs.playlist_videos',
            'aside_menu'    => false, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.playlist_videos.singular',
            'plural'        => 'backend::cruds.contents.playlist_videos.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'    => [
                        'preview' => '500x375',
                        '85x85',
                        '180x180',
                        '795x259',
                        '861x825',
                    ]
                ],
                'categories'        => [],
                'title'             => ['required' => true],
                'brief'             => [],
                'description'       => [],
                'link'              => ['required' => true],
                'sort_order'        => [],
                'tags'              => ['required' => false],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
            ]
        ],
        'agents' => [
            'icon'          => 'cms.svgs.agents',
            'aside_menu'    => false, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.agents.singular',
            'plural'        => 'backend::cruds.contents.agents.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'    => [
                        'preview' => '250x320',
                        '85x85',
                        '500x640',
                    ]
                ],
                'title'         => ['required' => true],
                'brief'         => ['required' => true],
                'description'   => [],
                'link'          => [],
                'sort_order'    => [],
                'categories'    => ['required' => true],
            ],
            'custom_fields' => [
                'facebook'  => [
                    'required'      => true,
                    'type'          => 'text',
                ],
                'email'  => [
                    'required'      => true,
                    'type'          => 'text',
                ],
                'phone_number'  => [
                    'required'      => true,
                    'type'          => 'text',
                ]
            ],
        ],
        'testimonials' => [
            'icon'          => 'cms.svgs.testimonials',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.testimonials.singular',
            'plural'        => 'backend::cruds.contents.testimonials.plural',
            'fields'        => [
                // 'slug'          => ['required' => true],
                'image'         => [
                    'dimensions'        => [
                        'preview' => '110x110',
                        '85x85',
                        '270x270',
                    ]
                ],
                'title'         => ['required' => true],
                'brief'         => ['required' => true],
                'description'   => [],
                'link'          => [],
                'sort_order'    => [],
                // 'tags'              => ['required' => false],
                // 'keywords'          => ['required' => false],
                // 'seo_description'   => ['required' => false],
            ]
        ],
        'services' => [
            'icon'          => 'cms.svgs.services',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.services.singular',
            'plural'        => 'backend::cruds.contents.services.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'dimensions'        => [
                        'preview' => '85x85',
                        '75x75',
                        '300x300',
                    ]
                ],
                'title'             => ['required' => true],
                'brief'             => [],
                'description'       => [],
                // 'link'              => [],
                'sort_order'        => [],
                'tags'              => ['required' => false],
                'about'             => ['required' => false],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
                'currency_icon'     => [],
            ]
        ],
        'achievements' => [
            'icon'          => 'cms.svgs.services',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.achievements.singular',
            'plural'        => 'backend::cruds.contents.achievements.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'dimensions'        => [
                        'preview' => '250x250',
                        '75x75',
                        '400x400',
                        '600x600',
                        '1000x750'
                    ]
                ],
                'title'             => ['required' => true],
                'brief'             => [],
                'description'       => [],
                'sort_order'        => [],
                'tags'              => ['required' => false],
                'about'             => ['required' => false],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
            ]
        ],
        'pages' => [
            'icon'          => 'cms.svgs.pages',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.pages.singular',
            'plural'        => 'backend::cruds.contents.pages.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'dimensions'        => [
                        'preview' => '640x180',
                        '85x85',
                        '1920x540',
                    ]
                ],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => ['required' => true],
                'link'          => [],
                'sort_order'    => [],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
                'tags'          => [],
                'keywords'      => ['required' => false],
            ]
        ],
        'offices' => [
            'icon'          => 'cms.svgs.offices',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.contents.offices.singular',
            'plural'        => 'backend::cruds.contents.offices.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'    => [
                        'preview' => '150x100',
                        '85x85',
                        '450x300',
                    ]
                ],
                'title'         => ['required' => true],
                'brief'         => [],
                'description'   => [],
                'link'          => [],
                'sort_order'    => []
            ]
        ],
        'filters' => [
            'icon'          => 'cms.svgs.filters',
            'aside_menu'    => true, // Appears on aside menu?
            'singular'      => 'backend::cruds.contents.filters.singular',
            'plural'        => 'backend::cruds.contents.filters.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                // 'image'         => [
                //     'required'      => true,
                //     'dimensions'    => [
                //         'preview' => '200x150',
                //         '85x85',
                //         '100x70',
                //         '590x330',
                //     ]
                // ],
                'categories'        => ['required' => false],
                'title'             => ['required' => true],
                'brief'             => ['required' => true],
                'description'       => ['required' => true],
                'link'              => ['required' => true],
                'keywords'          => ['required' => false],
                'seo_description'   => ['required' => false],
                'sort_order'        => [],
                'tags'              => ['required' => false],
                'link_trans'        => ['required' => true],
            ]
        ],
        'advertisements'=> [
            'icon'          => 'cms.svgs.advertisements',
            'aside_menu'    => true, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.advertisements.singular',
            'plural'        => 'backend::cruds.contents.advertisements.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'title'         => ['required' => true],
                'link'          => ['required' => true],
                'sort_order'    => []
            ]
        ],
        'currencies' => [
            'icon'          => 'cms.svgs.currencies',
            'aside_menu'    => false, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.currencies.singular',
            'plural'        => 'backend::cruds.contents.currencies.plural',
            'fields'        => [
                'slug'              => ['required' => true],
                'image'             => [
                    'required'      => true,
                    'dimensions'    => [
                        'preview' => '200x150',
                        '85x85',
                        '100x70',
                        '590x330',
                    ]
                ],
                'title'             => ['required' => true],
                'sort_order'        => [],
                'currency_code'     => ['required' => true],
                'currency_symbol'   => ['required' => true],
                'currency_icon'     => [],
                'currency_value'    => ['required' => true],
            ]
        ],
        'first_banners'=> [
            'icon'          => 'cms.svgs.banners',
            'aside_menu'    => false, // Appears on aside menu?
            'singular'      => 'backend::cruds.contents.first_banners.singular',
            'plural'        => 'backend::cruds.contents.first_banners.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'    => [
                        'preview' => '200x200',
                        '85x85',
                        '200x200',
                        '500x500',
                        '1000x1000',
                    ]
                ],
                'image_first_banners' => [],
                'title'         => ['required' => true],
                'sort_order'    => [],
                'link'          => [],
            ]
        ],
        'second_banners'=> [
            'icon'          => 'cms.svgs.banners',
            'aside_menu'    => false, // Appears on aside menu?
            'singular'      => 'backend::cruds.contents.second_banners.singular',
            'plural'        => 'backend::cruds.contents.second_banners.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'    => [
                        'preview' => '200x200',
                        '85x85',
                        '200x200',
                        '500x500',
                        '1000x1000',
                    ]
                ],
                'second_first_banners'  => [],
                'title'         => ['required' => true],
                'sort_order'    => [],
                'link'          => [],
            ]
        ],
        'balance' => [
            'icon'          => 'cms.svgs.balance',
            'aside_menu'    => false, // Appears on aside menu?
            'attachments'   => false,
            'singular'      => 'backend::cruds.contents.balance.singular',
            'plural'        => 'backend::cruds.contents.balance.plural',
            'fields'        => [
                'slug'          => ['required' => true],
                'image'         => [
                    'required'      => false,
                    'dimensions'    => [
                        'preview' => '590x330',
                        '85x85',
                        '590x330',
                    ]
                ],
                'title'         => ['required' => true],
                'brief'         => ['required' => true],
                'description'   => [],
                'link'          => [],
                'sort_order'    => []
            ]
        ],
    ];
    /**
     * Returns a collection of available types in the content model.
     */
    public function seoDescription(){
        if(!empty($this->translateOrFirst()->seo_description)){
            $seo_description = $this->translateOrFirst()->seo_description;
        }else{
            $seo_description = $this->translateOrFirst()->brief;
        }
        return $seo_description;
    }
    public function CustomFields()
    {
        return $this->hasMany(Attributes::class,'content_id','id');
    }

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

    /**
     * Returns an array of a content type's field list.
     */
    public static function getTypeFields($type)
    {
        $typeInfo = self::getTypeInfo($type);

        // if(! isset($typeInfo['fields'])) throw new \Exception("Index 'fields' does not exists for '{$type}' type.", 404);
        if(! isset($typeInfo['fields'])) return false;

        return $typeInfo['fields'];
    }
    public static function getTypeCustomFields($type)
    {
        $typeInfo = self::getTypeInfo($type);


        if(! isset($typeInfo['custom_fields'])) return false;

        return $typeInfo['custom_fields'];
    }

    /**
     * Returns an array of a content type's field names list.
     */
    public static function getTypeFieldNames($type)
    {
        return array_keys(self::getTypeFields($type));
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
                return __('cms::cruds.contents.'.$field.'.label', $params);
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
                return __('cms::cruds.contents.'.$field.'.placeholder', $params);
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
                return __('cms::cruds.contents.'.$field.'.help', $params);
            }
        }

        throw new \Exception("'{$type}' type does not have '{$field}' field.", 404);
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
    public static function iconDimensionsByType($type){
        if(static::typeHasField($type, 'currency_icon'))
        {
            $iconOptions = self::getTypeFields($type)['currency_icon'];

            if(!empty($iconOptions) && isset($iconOptions['dimensions']) &&  !empty($iconOptions['dimensions']))
            {
                return collect($iconOptions['dimensions']);
            }
            else
            {
                return collect(self::$iconOptions['dimensions']);
            }
        }

        return collect([]);
    }

    public  function AllCategoryParents($category,$result = [],$collection = null){
        $collection = Category::where('type', $this->type)->get();
        $parent_category = is_null($collection) ? Category::find($category->parent_id) : $collection->where('id',$category->parent_id)->first();
        if(!is_null($parent_category)){
            $result[] = $parent_category;
            $result = array_merge(Category::AllCategoryParents($parent_category,[],$collection),$result);
        }
        return $result;
    }

    public static function getTypeAttachmentValidationRules($type){
        if(static::typeHasField($type, 'attachments'))
        {
            $attachmentOptions = self::getTypeFields($type)['attachments'];

            if(!empty($attachmentOptions) && isset($attachmentOptions['validation_rules']))
            {
                return $attachmentOptions['validation_rules'];
            }
            else
            {
                return 'required|file|max:2048|mimes:jpeg,jpg,png,pdf';
            }
        }

        return '';
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable', 'cms_categorizables');
    }
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'cms_taggables');
    }
    public function externalAttachments()
    {
        return $this->morphMany(ExternalAttachments::class, 'attachable');
        // $related, $name, $type = null, $id = null, $localKey = null
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    protected static $imageOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
            '400x400',
            'autox400',
            '1000x750',
            '1000x1000',
        ],
    ];
    protected static $iconOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
        ],
    ];

    public static function getImage($model, $size = '85x85', $type = 'contents'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($model->image, 'http')){
            return $model->image;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if($type != 'contents')
        {
            if(($size != '85x85') && (! self::imageDimensionsByType($type)->contains($size))){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        else
        {
            if(($size != '85x85') && (! self::imageDimensions()->contains($size))){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        return ($model->image)
        ? route('image', ['size' => $size, 'path' => $model->image])
        : route('image', [
            'size' => $size,
            // 'path' => 'defaults/'.$type.'.png'
            'path' => in_array($type, self::$contentWithSpecificDefaults) ? 'defaults/'.$type.'.png' : 'defaults/contents.png'

        ]);
    }
    public static function getIconImage($model, $size = '85x85', $type = 'contents'){
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($model->currency_icon, 'http')){
            return $model->currency_icon;
        }
        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if($type != 'contents')
        {
            if(! self::imageDimensionsByType($type)->contains($size) && $size != 'original'){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        else
        {
            if(! self::imageDimensions()->contains($size) && $size != 'original'){
                return route('image', ['size' => $size, 'path' => 'not_found.png']);
            }
        }
        return ($model->currency_icon)
        ? route('image', ['size' => $size, 'path' => $model->currency_icon])
        : route('image', [
            'size' => $size,
            'path' => 'defaults/'.$type.'.png',
            'path' => in_array($type, self::$contentWithSpecificDefaults) ? 'defaults/'.$type.'.png' : 'defaults/contents.png'

        ]);
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
    public static function imageDimensions(){
        return collect(self::$imageOptions['dimensions']);
    }
    public function formAjaxArray($is_selected = true){
        return [
            'id'        =>  $this->id,
            'text'      => $this->translateOrFirst(app()->getLocale())->title,
            'selected'  =>  $is_selected,
        ];
    }
    public function scopeContentType($query,$type = []){
        return $query->when(!empty($type), function($q) use ($type) {
            $q->whereIn('type', $type);
        });
    }
}
