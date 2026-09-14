<?php

namespace Modules\Cms\Entities\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Cms\Entities\Traits\Disabable;
use Modules\Cms\Entities\UserType;
use Illuminate\Support\Str;

trait CmsUser
{
    use SoftDeletes, Disabable;

    /**
     * User Statuses
     */
    protected static $statuses = [
        'PENDING' => [
            'code'          => 'PENDING',
            'option_class'  => 'm--font-warning',
            'label'         => 'cms::users.statuses.PENDING.label'
        ],
        'SUSPENDED' => [
            'code'          => 'SUSPENDED',
            'option_class'  => 'm--font-danger',
            'label'         => 'cms::users.statuses.SUSPENDED.label'
        ],
        'ACTIVE' => [
            'code'          => 'ACTIVE',
            'option_class'  => 'm--font-success',
            'label'         => 'cms::users.statuses.ACTIVE.label'
        ],
    ];

    /**
     * Sex
     */
    protected static $sexOptions = [
        'UNSPECIFIED' => [
            'code'          => 'UNSPECIFIED',
            'option_class'  => 'm--font-secondary',
            'label'         => 'cms::users.sexOptions.UNSPECIFIED.label'
        ],
        'MALE' => [
            'code'          => 'MALE',
            'option_class'  => 'm--font-info',
            'label'         => 'cms::users.sexOptions.MALE.label'
        ],
        'FEMALE' => [
            'code'          => 'FEMALE',
            'option_class'  => 'm--font-info',
            'label'         => 'cms::users.sexOptions.FEMALE.label'
        ],
    ];

    /**
     * Image Options
     */
    protected static $imageOptions = [
        'dimensions' => [
            '75x75',
            '85x85',
            '150x150',
            '400x400',
            '1000x1000',
        ],
    ];

    // Get a collection of the allowed image dimensions
    public static function imageDimensions()
    {
        return collect(self::$imageOptions['dimensions']);
    }

    /**
     * Model Scopes
     */
    protected static function boot()
    {
        parent::boot();
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q)
        {
            $q->where('status', 'ACTIVE');
        });
    }

    public function scopeVerified($query)
    {
        return $query->where(function ($q)
        {
            $q->where('verification_code', 'VERIFIED');
        });
    }

    /**
     * Model Relations
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getImage($size = '85x85')
    {
        // This means that the image is not actually stored internaly but rather externally.
        if(Str::startsWith($this->image, 'http'))
        {
            return $this->image;
        }

        // If the image size provided in the url not within the allowed dimensions, a not-found image will be returned.
        if(! self::imageDimensions()->contains($size))
        {
            return route('image', ['size' => $size, 'path' => 'not_found.png']);
        }

        return ($this->image)
        ? route('image', ['size' => $size, 'path' => $this->image])
        : route('image', ['size' => $size, 'path' => 'defaults/users.png']);
    }

    public static function collectStatuses()
    {
        return collect(static::$statuses);
    }

    public static function collectSexOptions()
    {
        return collect(static::$sexOptions);
    }

    public function isApproved()
    {
        return $this->status == 'ACTIVE';
    }
}
