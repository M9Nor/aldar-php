<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public $ipAddresses = ['176.33.111.147'];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Laravel 7 pagination links: see App\Pagination\LengthAwarePaginator.
        $this->app->bind(\Illuminate\Pagination\LengthAwarePaginator::class, \App\Pagination\LengthAwarePaginator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();
        Carbon::setLocale(app()->getLocale());
        setlocale(LC_TIME,'ar_BH');
        Schema::defaultStringLength(191);

        if(in_array(request()->ip(), $this->ipAddresses))
        {
            config(['app.debug' => true]);

            // Delete this if you aren't using the Laravel Debugbar package.
            \Debugbar::enable();

            // Uncomment the next line when the configurations are cached in the project your working on.
            // \Artisan::call('cache:clear');
        }
    }
}
