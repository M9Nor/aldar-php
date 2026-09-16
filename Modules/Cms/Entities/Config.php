<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\Traits\Disabable;
use Illuminate\Support\Str;

class Config extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes, Disabable;
    protected $table            = 'cms_configs';
    protected $guarded          = [];
    public $translationModel    = 'Modules\Cms\Entities\ConfigTranslation';
    protected $fillable = [
        'sort_order',
        'validations',
        'input_type',
        'key',
        'val',
        'has_additional_info',
        'url_link',
        'disabled_at',
        'deleted_at',
    ];
    public $translatedAttributes = [
        'label',
        'placeholder',
        'help',
        'icon',
        'image',
        'title',
        'description',
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
