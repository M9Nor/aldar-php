<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\Traits\Disabable;
use Modules\Cms\Entities\Content;
use Modules\Backend\Entities\Project;
use Illuminate\Support\Str;

class Tag extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes, Disabable;
    protected $table = 'cms_tags';
    public $translationModel = 'Modules\Cms\Entities\TagTranslation';

    protected $fillable = [
        'type',
        'views',
        'added_by',
    ];

    public $translatedAttributes = [
        'text',
        'description',
        'image'
    ];

    // public function contents()
    // {
    //     return $this->morphedByMany(Content::class, 'categorizable', 'cms_categorizables');
    // }
    
}
