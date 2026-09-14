<?php

namespace Modules\Permissions\Entities;

use Illuminate\Database\Eloquent\Model;
use Silber\Bouncer\Database\Ability as BaseAbility;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Permissions\Entities\AbilityGroup;

class Ability extends BaseAbility
{
    use Translatable, TranslatableHelper, SoftDeletes;

    protected $fillable = [
        'name'
    ];

    public $translationModel = 'Modules\Permissions\Entities\AbilityTranslation';

    public $translatedAttributes = [
        'title',
        'description'
    ];

    public function group()
    {
        return $this->belongsTo(AbilityGroup::class, 'group_id', 'id');
    }

    /**
     * Returns the ability's group title with a optional suffex.
     * i.e. {User Management - }
     */
    public function getGroupTitleAttribute($suffex = '')
    {
        return !is_null($group = $this->group) ? $group->translateOrFirst()->title . $suffex : '';
    }
}
