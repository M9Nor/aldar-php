<?php

namespace Modules\Cms\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;

use Modules\Cms\Policies\UserPolicy;
use Modules\Cms\Policies\CategoryPolicy;
use Modules\Cms\Policies\ContentPolicy;
use Modules\Cms\Policies\AreaPolicy;
use Modules\Cms\Policies\CityPolicy;
use Modules\Cms\Policies\LandingPagePolicy;
use Modules\Cms\Policies\CountryPolicy;
use Modules\Cms\Policies\TagPolicy;
use Modules\Cms\Policies\ConfigPolicy;
use Modules\Permissions\Policies\RolePolicy;
use Modules\Backend\Policies\ProjectPolicy;
use Modules\Notification\Policies\NotificationPolicy;

use App\User;
use Modules\Cms\Entities\Category;
use Modules\Cms\Entities\Content;
use Modules\Cms\Entities\Config;
use Modules\Cms\Entities\Area;
use Modules\Cms\Entities\City;
use Modules\Cms\Entities\LandingPage;
use Modules\Cms\Entities\Country;
use Modules\Cms\Entities\Tag;
use Modules\Permissions\Entities\Role;
use Modules\Backend\Entities\Project;
use Modules\Notification\Entities\FirebaseNotification;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class                 => UserPolicy::class,
        Role::class                 => RolePolicy::class,
        Category::class             => CategoryPolicy::class,
        Content::class              => ContentPolicy::class,
        Config::class               => ConfigPolicy::class,
        Tag::class                  => TagPolicy::class,
        Project::class              => ProjectPolicy::class,
        Area::class                 => AreaPolicy::class,
        City::class                 => CityPolicy::class,
        LandingPage::class          => LandingPagePolicy::class,
        Country::class              => CountryPolicy::class,
        FirebaseNotification::class => NotificationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Request::macro('subdomain', function () {
            return current(explode('.', $this->getHost()));
        });
        //
    }

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
}
