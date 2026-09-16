<?php

namespace App\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as BaseLengthAwarePaginator;

/**
 * Laravel 7 pagination parity: the framework length-aware paginator, with the Laravel 7.3.0
 * URL window.
 *
 * Laravel 13's Illuminate\Pagination\UrlWindow shows more page links around the current page
 * than Laravel 7's did, which adds page links to every paginated listing rendered with
 * `->links()`. AppServiceProvider binds this class in place of the framework paginator, which
 * the query and Eloquent builders resolve through the container in paginate(). The
 * elements() body is unchanged from v7.3.0 apart from the UrlWindow class it uses.
 */
class LengthAwarePaginator extends BaseLengthAwarePaginator
{
    /**
     * Get the array of elements to pass to the view.
     *
     * @return array
     */
    #[\Override]
    protected function elements()
    {
        $window = UrlWindow::make($this);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
}
