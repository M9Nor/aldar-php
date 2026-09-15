<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use View;
use App\User;

use Modules\Notification\Http\Exceptions\PermissionsException;
use Modules\Notification\Entities\FirebaseNotification;
use Modules\Notification\Entities\FirebaseNotificationReceiver;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Notification';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'notification';

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
        $this->registerEvents();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        FirebaseNotificationReceiver::created(function($Receiver){
            FirebaseNotificationReceiver::prepareToSend($Receiver);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
    protected function registerEvents()
    {
        User::created(function($User){
        event('UserController@store', [
            request(), $User,
        ]);
        try {
            app()->make('Cms')->startSendingNotifications();
        }
        catch (\Exception $th) {

        }
    });

    \Event::listen('UserController@store', function($request, $User){

        $newNotification = FirebaseNotification::where('type', 'NEW_USER')->first();

        if (empty($newNotification)) {
            return null;
        }
        $Users = User::where('status', 'ACTIVE')->get();

        foreach ($Users as $key => $User) {
            $UserToNotify                   = new FirebaseNotificationReciver;
            $UserToNotify->notification_id  = $newNotification->id;
            $UserToNotify->status           = 'DELIVERED';
            $UserToNotify->triggered_by     = 'ACTION';
            $UserToNotify->from_user_id     = $User->id;
            $UserToNotify->to_user_id       = $User->id;
            $UserToNotify->save();
            $UserToNotify->Data()->createMany([

                [
                    'notification_id' => $newNotification->id,
                    'x_key'           => 'request_username',
                    'x_val'           => $request->username,
                ],
            ]);
        }
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
        if (! class_exists(Factory::class)) {
            return; // Laravel 8 removed the legacy factory loader; these modules define no class-based factories.
        }

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
