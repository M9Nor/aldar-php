<?php

namespace Modules\Cms\Classes;

use Modules\Cms\Entities\Traits\Helpers;

/**
 * A concrete class for calling Helpers::parseDate() statically.
 *
 * PHP 8.1 deprecates calling a static trait method, or reading a static trait property, on the
 * trait itself, so views and controllers call DateHelper::parseDate() instead of
 * Helpers::parseDate(). The method body is the trait's and its output is unchanged. The cached
 * I18N_Arabic_Date parser now lives in DateHelper::$DateParser instead of Helpers::$DateParser;
 * it keeps no per-call state (its mode is set to 4 once), so where it is cached does not matter.
 */
class DateHelper
{
    use Helpers;
}
