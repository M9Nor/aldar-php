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

class PropertyForm extends Model
{
    use SoftDeletes, Disabable, Helpers;
    
    protected $table = 'property_form';

    public function attachments(){
        return $this->morphMany(Attachment::class,'attachable');
    }

}