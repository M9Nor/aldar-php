<?php

namespace Modules\Permissions\Entities;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Modules\Cms\Entities\Traits\TranslatableHelper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Permissions\Entities\Ability;

class AbilityGroup extends Model
{
    use Translatable, TranslatableHelper, SoftDeletes;

    protected $table = 'perms_groups';

    protected $fillable = [
        'name'
    ];

    public $translationModel = 'Modules\Permissions\Entities\AbilityGroupTranslation';

    public $translationForeignKey = 'group_id';

    public $translatedAttributes = [
        'title',
        'description'
    ];

    public function abilities()
    {
        return $this->hasMany(Ability::class, 'group_id', 'id');
    }
}
