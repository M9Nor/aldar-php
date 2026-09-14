<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group([
    'prefix'        => LaravelLocalization::setLocale(),
    'middleware'    => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function()
{
	Route::group(['prefix' => 'admin'], function() {
        Route::prefix('notification')->group(function() {
            Route::get('/'               ,'NotificationController@index')->name('NotificationsController@index');
            Route::post('/'              ,'NotificationController@postIndex')->name('NotificationsController@postIndex');
            Route::get('config'          ,'NotificationController@getConfig')->name('NotificationsController@getConfig');
            Route::post('postWebToken'   ,'NotificationController@postWebToken')->name('NotificationsController@postWebToken');
            Route::post('getList'        ,'NotificationController@getList')->name('NotificationsController@getList');
            Route::get('create'          ,'NotificationController@create')->name('NotificationsController@create');
            Route::post('postCreate'     ,'NotificationController@postCreate')->name('NotificationsController@postCreate');
        });
    });
});
