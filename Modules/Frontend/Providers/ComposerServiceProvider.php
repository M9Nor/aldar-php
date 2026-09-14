<?php

namespace Modules\Frontend\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    public function boot()
    {
        View::composer([
            'frontend::layouts.master',
            'frontend::index.who_we_are',
            'frontend::properties.single',
            'frontend::opportunity.*',
        ], 'Modules\Frontend\Http\ViewComposers\MenuComposer');
        View::composer([
            'frontend::layouts.index',
            'frontend::index.slider',
            'frontend::filter.index',
            'frontend::opportunity.*',
        ], 'Modules\Frontend\Http\ViewComposers\FilterComposer');

    }
}
