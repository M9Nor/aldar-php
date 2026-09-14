<?php

namespace Modules\Notification\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dimsav\Translatable\Translatable;
use Modules\Notification\Entities\Traits\TranslatableHelper;
use Modules\Notification\Entities\Traits\Disabable;

class Config extends Model
{
    use Translatable,
        TranslatableHelper;
    //     SoftDeletes,
    //     Disabable;

    public $timestamps = false;

    public static $allConfigs = [];

    protected $fillable = [
    ];

    public static function loadAll (array $configs, $lang = null)
    {
        static::$allConfigs = $configs;
    }

    public static function setByKey (string $config, $val)
    {
        static::$allConfigs[ $config ] = $val;
    }

    public static function of ($attr, $default = null, $lang = null)
    {
        if (isset(static::$allConfigs[ $attr ])) {
            return is_null(static::$allConfigs[ $attr ]) ? $default : static::$allConfigs[ $attr ];
        }
        return $default;
    }
}
