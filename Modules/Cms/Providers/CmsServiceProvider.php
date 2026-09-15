<?php

namespace Modules\Cms\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use View;

use LaravelLocalization;

class CmsServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['config']['filesystems.disks.graph'] = [
            'driver' => 'local',
            'root' => public_path('graph/uploads/original'),
        ];

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ImageServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);

        $this->app->singleton('Cms', function($app){
            return new \Modules\Cms\Classes\Core;
        });

        // laravel-modules 13 registers module providers in the register phase (v7 did it while booting),
        // before deferred services such as the translator that LaravelLocalization needs exist.
        $this->app->booting(function () {
            View::share('currentLang', LaravelLocalization::getCurrentLocale());
            View::share('supportedLangs', LaravelLocalization::getLocalesOrder());
            View::share('currentLangName', LaravelLocalization::getCurrentLocaleName());
            View::share('currentLangNative', LaravelLocalization::getCurrentLocaleNative());
            View::share('langDirection', LaravelLocalization::getCurrentLocaleDirection());
        });
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('cms.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'cms'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/cms');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/cms';
        }, \Config::get('view.paths')), [$sourcePath]), 'cms');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/cms');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'cms');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'cms');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! class_exists(Factory::class)) {
            return; // Laravel 8 removed the legacy factory loader; these modules define no class-based factories.
        }

        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
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
}
