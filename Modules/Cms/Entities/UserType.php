<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;

class UserType extends Model
{
    use Translatable, TranslatableHelper;

    protected $table = 'user_types';

    public $translationModel = 'Modules\Cms\Entities\UserTypeTranslation';

    protected $fillable = [
        'name',
        'color',
    ];

    public $translatedAttributes = [
        'title',
        'description',
    ];
}
