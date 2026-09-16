<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
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
    }
}
