<?php

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

view()->composer('cms::includes.header_menu', function($view) {

    $userItems = [];

    if(auth()->user()->can('view', \App\User::class))
    {
        $userItems[] = [
            'label'     => __('cms::includes.aside.show_all'),
            'link'      => route('UserController@index'),
            'icon'      => 'flaticon2-list-2'
        ];
    }

    if(auth()->user()->can('create', \App\User::class))
    {
        $userItems[] = [
            'label'     => __('cms::includes.aside.create'),
            'link'      => route('UserController@create'),
            'icon'      => 'flaticon2-add'
        ];
    }

    if(!empty($userItems))
    {
        app()->make('Cms')->headerMenu([
            'header'    => __('cms::includes.aside.groups.user_management'),
            'label'     => __('cms::includes.aside.users'),
            'link'      => route('UserController@index'),
            'ordering'  => -1,
            'items'     => $userItems,
        ]);
    }

    $roleItems = [];

    if(auth()->user()->can('view', \Modules\Permissions\Entities\Role::class))
    {
        $roleItems[] = [
            'label'     => __('cms::includes.aside.show_all'),
            'link'      => route('RoleController@index'),
            'icon'      => 'flaticon2-list-2'
        ];
    }

    if(auth()->user()->can('create', \Modules\Permissions\Entities\Role::class))
    {
        $roleItems[] = [
            'label'     => __('cms::includes.aside.create'),
            'link'      => route('RoleController@create'),
            'icon'      => 'flaticon2-add'
        ];
    }

    if(!empty($roleItems))
    {
        app()->make('Cms')->headerMenu([
            'header'    => __('cms::includes.aside.groups.role_management'),
            'label'     => __('cms::includes.aside.roles'),
            'link'      => route('RoleController@index'),
            'items'     => $roleItems,
            'ordering'  => 1
        ]);
    }
});
