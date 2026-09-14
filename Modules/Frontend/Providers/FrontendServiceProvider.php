<?php

namespace Modules\Frontend\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

use Modules\Cms\Entities\Config;
use Modules\Cms\Entities\Content;
use Modules\Backend\Entities\Project;
use Modules\Cms\Entities\Tag;
use Carbon\Carbon;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Frontend';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'frontend';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Carbon::setLocale(app()->getLocale());

        // setlocale(LC_TIME, 'ar_BH');

        Carbon::setUTF8(true);

        if (!Cookie::get('default-currency'))
        {
            Cookie::queue('default-currency', 'TRY');
        }

        View::share('curr_locale', app()->getLocale());

        View::share('currentLang', \LaravelLocalization::getCurrentLocale());
        View::share('supportedLangs', \LaravelLocalization::getLocalesOrder());
        View::share('currentLangName', \LaravelLocalization::getCurrentLocaleName());
        View::share('currentLangNative', \LaravelLocalization::getCurrentLocaleNative());
        View::share('langDirection', \LaravelLocalization::getCurrentLocaleDirection());

        $popularTags[app()->getLocale()] = Cache::remember('popular_tags_' . app()->getLocale(), 60 * 60 * 24, function() {
            $tagsQuery = Tag::translatedIn(app()->getLocale())->with(['translations']);

            return $tagsQuery->whereNotNull('views')->orderBy('views', 'DESC')->limit(10)->get();
        });

        $featuredProjects[app()->getLocale()] = Cache::rememberForever('featured_projects_' . app()->getLocale(), function() {
            $projectsQuery = Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
         $query->where('type','property_classifications');
        })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function($q) {
                $q->where('type', 'property_classifications')->whereNotNull('parent_id');
            },'allCategories.translations', 'attachments' => function($query) {
                $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external')->orWhere('input_name', 'slider_images');
            }]);

            return $projectsQuery->orderBy('views', 'DESC')->limit(6)->get();
        });

         $featuredProjectsOpp[app()->getLocale()] = Cache::rememberForever('featured_projects_opp_' . app()->getLocale(), function() {
            $projectsQuery = Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
         $query->where('type','opportunity_classifications');
        })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function($q) {
                $q->where('type', 'opportunity_classifications')->whereNotNull('parent_id');
            },'allCategories.translations', 'attachments' => function($query) {
                $query->where('input_name', 'featured_images')->orWhere('input_name', 'image_external')->orWhere('input_name', 'slider_images');
            }]);

            return $projectsQuery->orderBy('views', 'DESC')->limit(6)->get();
        });

        $recentProjects[app()->getLocale()] = Cache::rememberForever('recent_projects_' . app()->getLocale(), function() {
            $projectsQuery = Project::translatedIn(app()->getLocale())->whereHas('allCategories', function ($query){
         $query->where('type','property_classifications');
        })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function($q) {
                $q->where('type', 'property_classifications')->whereNotNull('parent_id');
            },'allCategories.translations']);

            return $projectsQuery->orderBy('id', 'DESC')->limit(6)->get();
        });

        $recentProjectsOpp[app()->getLocale()] = Cache::rememberForever('recent_projects_opp_' . app()->getLocale(), function() {
            $projectsQuery = Project::translatedIn(app()->getLocale())->where('deleted_at',null)->whereHas('allCategories', function ($query){
         $query->where('type','opportunity_classifications');
        })->with(['translations', 'contracts.translations', 'city.translations', 'area.translations', 'prices' => function($query) {
                $query->orderBy('lowest_price');
            }, 'allCategories' => function($q) {
                $q->where('type', 'opportunity_classifications')->whereNotNull('parent_id');
            },'allCategories.translations']);

            return $projectsQuery->orderBy('id', 'DESC')->limit(6)->get();
        });


        
        $firstBanners[app()->getLocale()] =  Cache::remember('first_banners_'.app()->getLocale(), 1440, function () {
            return  Content::translatedIn(app()->getLocale())->with('translations')->where('type', 'first_banners')->get();
        });
        if ($firstBanners[app()->getLocale()]->count()  != 0) {
            $firstBanners[app()->getLocale()] = $firstBanners[app()->getLocale()]->random();
        } else {
            $firstBanners[app()->getLocale()] = null;
        }

        $secondBanners[app()->getLocale()] =  Cache::remember('second_banners_'.app()->getLocale(), 1440, function () {
            return  Content::translatedIn(app()->getLocale())->with('translations')->where('type', 'second_banners')->get();
        });
        if ($secondBanners[app()->getLocale()]->count()  != 0) {
            $secondBanners[app()->getLocale()] = $secondBanners[app()->getLocale()]->random();
        } else {
            $secondBanners[app()->getLocale()] = null;
        }

        View::share('popularTags', $popularTags);
        View::share('featuredProjects', $featuredProjects);
        View::share('featuredProjectsOpp', $featuredProjectsOpp);
        View::share('recentProjectsOpp', $recentProjectsOpp);
        View::share('recentProjects', $recentProjects);
        View::share('firstBanners', $firstBanners);
        View::share('secondBanners', $secondBanners);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ComposerServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        // View::composer(['frontend::layouts.master', 'frontend::layouts.index', 'frontend::articles.single', 'frontend::services.single', 'frontend::pages.contact_us', 'frontend::pages.page'], function ($view) {
        //     $style_version = Config::where('key', 'styles-version')->first();

        //     $contents = Cache::remember('global_contents', 1, function () {
        //        return Config::with('translations')->get()->keyBy('key');
        //     });

        //     foreach ($contents as $key => $setting) {
        //         if(!empty($setting->translateOrFirst()->description))
        //         {
        //             $contents[$key]->value = $setting->translateOrFirst()->description;
        //         }
        //         else
        //         {
        //             $contents[$key]->value = $setting->val;
        //         }
        //     }

        //     $view->with(compact([
        //         'contents',
        //     ]));
        // });


    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
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

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
