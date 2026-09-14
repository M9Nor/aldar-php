<?php

namespace Modules\Cms\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\ExternalAttachments;
use Illuminate\Support\Str;

class ExternalAttachments extends Model
{
    use SoftDeletes;
    protected $table = 'cms_external_attachments';
    protected $guarded = [];
    protected $fillable = [
        'type',
        'name',
        'description',
        'link',
        'attachable_type',
        'attachable_id',
        'deleted_at',
    ];
    // public function attachable()
    // {
    //     return $this->morphTo();
    // }
}