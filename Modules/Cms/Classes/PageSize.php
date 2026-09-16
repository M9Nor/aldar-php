<?php

namespace Modules\Cms\Classes;

use Illuminate\Http\Request;

/**
 * Page size for the Select2 JSON endpoints (S1).
 *
 * Every Blade caller sends items_per_page=20, so 20 is the cap: a larger value is clamped to it. A missing,
 * zero, negative or non-numeric value returns null, which makes paginate() use the model's default page
 * size (15), exactly as a missing value always did.
 */
class PageSize
{
    public const SELECT2_MAX = 20;

    public static function fromRequest(Request $request, int $max = self::SELECT2_MAX): ?int
    {
        $value = $request->input('items_per_page');

        if (! is_numeric($value) || (int) $value < 1) {
            return null;
        }

        return min((int) $value, $max);
    }
}
