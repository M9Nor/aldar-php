<?php

Route::get('img/{size}/{path}', 'ImageController@show')
    ->where([
        'size' => '^((\d+|auto)x(\d+|auto))|original$',
        'path' => '.*'
    ])
    ->name('image');

Route::group([
    'prefix'        => LaravelLocalization::setLocale(),
    'middleware'    => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function()
{
    Route::get('/get-countries',                    'CmsController@getCountries')->name('CmsController@getCountries');
    Route::get('/get-cities',                       'CmsController@getCities')->name('CmsController@getCities');
    Route::get('/get-areas',                        'CmsController@getAreas')->name('CmsController@getAreas');
    Route::get('get-contents',                      'CmsController@getContentsSelect2')->name('CmsController@getContentsSelect2');
	Route::group([
        'prefix'    => 'admin',
        'namespace' => 'Admin'
    ], function() {
        Route::get('/',                                 'DashboardController@index')->name('DashboardController@index');
        Route::post('update-currency',                  'DashboardController@updateCurrency')->middleware('staff')->name('DashboardController@updateCurrency');
        Route::get('clear-cache',                       '\Modules\Frontend\Http\Controllers\HomeController@clearCache')->middleware('staff')->name('cache.clear');

        Route::group(['prefix' => 'users'], function() {
            Route::get('/',                             'UserController@index')->name('UserController@index');
            Route::get('/data',                         'UserController@data')->name('UserController@data');
            Route::get('/create',                       'UserController@create')->name('UserController@create');
            Route::post('/store',                       'UserController@store')->name('UserController@store');
            Route::get('/summary',                      'UserController@summary')->name('UserController@summary');
            Route::get('/show',                         'UserController@show')->name('UserController@show');
            Route::get('/myprofile',                    'UserController@myprofile')->name('UserController@myprofile');
            Route::get('/{model}/edit',                 'UserController@edit')->name('UserController@edit');
            Route::post('/{model}/update',              'UserController@update')->name('UserController@update');
            Route::post('/{model}/update_profile',      'UserController@updateProfile')->name('UserController@update_profile');
            Route::post('/destroy/{model}',             'UserController@destroy')->name('UserController@destroy');
            Route::post('/mass_destroy',                'UserController@massDestroy')->name('UserController@massDestroy');
            Route::post('/restore',                     'UserController@restore')->name('UserController@restore');
            Route::post('/mass_restore',                'UserController@massRestore')->name('UserController@massRestore');
            Route::get('/login_as/{model}',             'UserController@loginAs')->name('UserController@loginAs');
            Route::get('/identity/validate',            'UserController@validateIdentity')->name('UserController@validateIdentity');
            Route::get('/identity/validate_',           'UserController@validateIdentity_')->name('UserController@validateIdentity_');
            Route::get('/get-user-select2',             'UserController@getUsersSelect2')->name('UserController@getUsersSelect2');
        });
        Route::group(['prefix' => 'contents'], function (){
            Route::get('{type}',                       'ContentController@index')->name('ContentController@index');
            Route::get('{type}/data',                  'ContentController@data')->name('ContentController@data');
            Route::get('{type}/create',                'ContentController@create')->name('ContentController@create');
            Route::post('{type}/store',                'ContentController@store')->name('ContentController@store');
            Route::get('{type}/{model}/edit',          'ContentController@edit')->name('ContentController@edit');
            Route::post('{type}/{model}/update',       'ContentController@update')->name('ContentController@update');
            Route::post('disable/{model}',             'ContentController@disable')->name('ContentController@disable');
            Route::post('enable/{model}',              'ContentController@enable')->name('ContentController@enable');
            Route::post('destroy/{model}',             'ContentController@destroy')->name('ContentController@destroy');
            Route::post('destroy/{model}/{locale}',    'ContentController@destroyTranslation')->name('ContentController@destroyTranslation');
            Route::post('mass_destroy',                'ContentController@massDestroy')->name('ContentController@massDestroy');
            Route::post('restore',                     'ContentController@restore')->name('ContentController@restore');
            Route::post('mass_restore',                'ContentController@massRestore')->name('ContentController@massRestore');
            Route::post('delete-attachemnt',           'ContentController@deleteAttachment')->name('ContentController@deleteAttachment');
        });
        Route::group(['prefix' => 'configs'], function (){
            Route::get('/',                            'ConfigController@index')->name('ConfigController@index');
            Route::get('/data',                        'ConfigController@data')->name('ConfigController@data');
            Route::get('/create',                      'ConfigController@create')->name('ConfigController@create');
            Route::post('/store',                      'ConfigController@store')->name('ConfigController@store');
            Route::get('/{model}/edit',                'ConfigController@edit')->name('ConfigController@edit');
            Route::post('/{model}/update',             'ConfigController@update')->name('ConfigController@update');
            Route::post('/destroy/{model}',            'ConfigController@destroy')->name('ConfigController@destroy');
            Route::post('/mass_destroy',               'ConfigController@massDestroy')->name('ConfigController@massDestroy');
            Route::post('/restore',                    'ConfigController@restore')->name('ConfigController@restore');
            Route::post('/mass_restore',               'ConfigController@massRestore')->name('ConfigController@massRestore');
            Route::get('/{model}/edit-config',         'ConfigController@editConfig')->name('ConfigController@editConfig');
            Route::post('/{model}/update-config',      'ConfigController@updateConfig')->name('ConfigController@updateConfig');
        });
        Route::group(['prefix' => 'categories'], function (){

            Route::get('asdwadwadwdaw',                'CategoryController@addCategoriesAndFilters')->name('CategoryController@addCategoriesAndFilters');
            Route::get('{type}',                       'CategoryController@index')->name('CategoryController@index');
            Route::get('{type}/data',                  'CategoryController@data')->name('CategoryController@data');
            Route::get('{type}/create',                'CategoryController@create')->name('CategoryController@create');
            Route::post('{type}/store',                'CategoryController@store')->name('CategoryController@store');
            Route::get('{type}/{model}/edit',          'CategoryController@edit')->name('CategoryController@edit');
            Route::post('{type}/{model}/update',       'CategoryController@update')->name('CategoryController@update');
            Route::post('destroy/{model}',             'CategoryController@destroy')->name('CategoryController@destroy');
            Route::post('destroy/{model}/{locale}',    'CategoryController@destroyTranslation')->name('CategoryController@destroyTranslation');
            Route::post('mass_destroy',                'CategoryController@massDestroy')->name('CategoryController@massDestroy');
            Route::post('restore',                     'CategoryController@restore')->name('CategoryController@restore');
            Route::post('mass_restore',                'CategoryController@massRestore')->name('CategoryController@massRestore');
            Route::get('{type}/get-categories',        'CategoryController@getCategoriesSelect2')->name('CategoryController@getCategoriesSelect2');
        });
        Route::group(['prefix' => 'landing_pages'], function (){

            Route::get('',                              'LandingPageController@index')->name('LandingPageController@index');
            Route::get('/data',                         'LandingPageController@data')->name('LandingPageController@data');
            Route::get('/create',                       'LandingPageController@create')->name('LandingPageController@create');
            Route::post('/store',                       'LandingPageController@store')->name('LandingPageController@store');
            Route::get('/{model}/edit',                 'LandingPageController@edit')->name('LandingPageController@edit');
            Route::post('/{model}/update',              'LandingPageController@update')->name('LandingPageController@update');
            Route::post('destroy/{model}',              'LandingPageController@destroy')->name('LandingPageController@destroy');
            Route::post('destroy/{model}/{locale}',     'LandingPageController@destroyTranslation')->name('LandingPageController@destroyTranslation');
            Route::post('mass_destroy',                 'LandingPageController@massDestroy')->name('LandingPageController@massDestroy');
            Route::post('restore',                      'LandingPageController@restore')->name('LandingPageController@restore');
            Route::post('mass_restore',                 'LandingPageController@massRestore')->name('LandingPageController@massRestore');
            Route::post('delete-timeline',              'LandingPageController@deleteTimeline')->name('LandingPageController@deleteTimeline');
        });
        Route::group(['prefix' => 'tags'], function (){
            Route::get('',                             'TagController@index')->name('TagController@index');
            Route::get('/list',                        'TagController@list')->name('TagController@list');
            Route::post('save',                        'TagController@save')->name('TagController@save');
            Route::get('data',                         'TagController@data')->name('TagController@data');
            Route::get('create',                       'TagController@create')->name('TagController@create');
            Route::post('store',                       'TagController@store')->name('TagController@store');
            Route::get('{model}/edit',                 'TagController@edit')->name('TagController@edit');
            Route::post('{model}/update',              'TagController@update')->name('TagController@update');
            Route::post('disable/{model}',             'TagController@disable')->name('TagController@disable');
            Route::post('enable/{model}',              'TagController@enable')->name('TagController@enable');
            Route::post('destroy/{model}',             'TagController@destroy')->name('TagController@destroy');
            Route::post('destroy/{model}/{locale}',    'TagController@destroyTranslation')->name('TagController@destroyTranslation');
            Route::post('mass_destroy',                'TagController@massDestroy')->name('TagController@massDestroy');
            Route::post('restore',                     'TagController@restore')->name('TagController@restore');
            Route::post('mass_restore',                'TagController@massRestore')->name('TagController@massRestore');
            Route::post('delete-attachemnt',           'TagController@deleteAttachment')->name('TagController@deleteAttachment');
        });
        Route::group(['prefix' => 'areas'], function (){
            Route::get('',                             'AreaController@index')->name('AreaController@index');
            Route::get('data',                         'AreaController@data')->name('AreaController@data');
            Route::get('create',                       'AreaController@create')->name('AreaController@create');
            Route::post('store',                       'AreaController@store')->name('AreaController@store');
            Route::get('{model}/edit',                 'AreaController@edit')->name('AreaController@edit');
            Route::post('{model}/update',              'AreaController@update')->name('AreaController@update');
            Route::post('disable/{model}',             'AreaController@disable')->name('AreaController@disable');
            Route::post('enable/{model}',              'AreaController@enable')->name('AreaController@enable');
            Route::post('destroy/{model}',             'AreaController@destroy')->name('AreaController@destroy');
            Route::post('destroy/{model}/{locale}',    'AreaController@destroyTranslation')->name('AreaController@destroyTranslation');
            Route::post('mass_destroy',                'AreaController@massDestroy')->name('AreaController@massDestroy');
            Route::post('restore',                     'AreaController@restore')->name('AreaController@restore');
            Route::post('mass_restore',                'AreaController@massRestore')->name('AreaController@massRestore');
            Route::post('delete-attachemnt',           'AreaController@deleteAttachment')->name('AreaController@deleteAttachment');
        });
        Route::group(['prefix' => 'cities'], function (){
            Route::get('',                             'CityController@index')->name('CityController@index');
            Route::get('data',                         'CityController@data')->name('CityController@data');
            Route::get('create',                       'CityController@create')->name('CityController@create');
            Route::post('store',                       'CityController@store')->name('CityController@store');
            Route::get('{model}/edit',                 'CityController@edit')->name('CityController@edit');
            Route::post('{model}/update',              'CityController@update')->name('CityController@update');
            Route::post('disable/{model}',             'CityController@disable')->name('CityController@disable');
            Route::post('enable/{model}',              'CityController@enable')->name('CityController@enable');
            Route::post('destroy/{model}',             'CityController@destroy')->name('CityController@destroy');
            Route::post('destroy/{model}/{locale}',    'CityController@destroyTranslation')->name('CityController@destroyTranslation');
            Route::post('mass_destroy',                'CityController@massDestroy')->name('CityController@massDestroy');
            Route::post('restore',                     'CityController@restore')->name('CityController@restore');
            Route::post('mass_restore',                'CityController@massRestore')->name('CityController@massRestore');
            Route::post('delete-attachemnt',           'CityController@deleteAttachment')->name('CityController@deleteAttachment');
        });
        Route::group(['prefix' => 'countries'], function (){
            Route::get('',                             'CountryController@index')->name('CountryController@index');
            Route::get('data',                         'CountryController@data')->name('CountryController@data');
            Route::get('create',                       'CountryController@create')->name('CountryController@create');
            Route::post('store',                       'CountryController@store')->name('CountryController@store');
            Route::get('{model}/edit',                 'CountryController@edit')->name('CountryController@edit');
            Route::post('{model}/update',              'CountryController@update')->name('CountryController@update');
            Route::post('disable/{model}',             'CountryController@disable')->name('CountryController@disable');
            Route::post('enable/{model}',              'CountryController@enable')->name('CountryController@enable');
            Route::post('destroy/{model}',             'CountryController@destroy')->name('CountryController@destroy');
            Route::post('destroy/{model}/{locale}',    'CountryController@destroyTranslation')->name('CountryController@destroyTranslation');
            Route::post('mass_destroy',                'CountryController@massDestroy')->name('CountryController@massDestroy');
            Route::post('restore',                     'CountryController@restore')->name('CountryController@restore');
            Route::post('mass_restore',                'CountryController@massRestore')->name('CountryController@massRestore');
            Route::post('delete-attachemnt',           'CountryController@deleteAttachment')->name('CountryController@deleteAttachment');
        });
        Route::group([ 'prefix' => 'attachments' ], function() {
            Route::post('/store',                      'AttachmentController@store')->name('AttachmentController@store');
            Route::post('delete',                      'AttachmentController@destroy')->name('AttachmentController@destroy');
        });
        Route::group(['prefix' => 'tinymce'], function (){
            Route::post('uploader',                    'TinymceController@uploader')->name('TinymceController@uploader');
        });
    });
    Route::group(['prefix' => '/authenticate'], function() {
        Auth::routes([
            'verify' => true,
            'register' => false
        ]);
    });
});
