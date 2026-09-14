<?php

use Modules\Notification\Entities\FirebaseNotification;
/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/

view()->composer('cms::includes.aside', function($view) {
    if(auth()->user()->can('create', FirebaseNotification::class))
    {
        // app()->make('Cms')->asideMenu([
        //     'label'     => __('notification::strings.send_notification'),
        //     'icon'      => config('cms.svgs.notifications'),
        //     'link'      => route('NotificationsController@create'),
        //     'items'     => [],
        //     'ordering'  => 2
        // ]);
    }
});
