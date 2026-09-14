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
	Route::group([
        'prefix'    => 'admin',
        'namespace' => 'Admin'
    ], function() {
        Route::group([
            'prefix'    => 'roles'
        ], function() {
            Route::get('/', 'RoleController@index')->name('RoleController@index');
            Route::get('/data', 'RoleController@data')->name('RoleController@data');
            Route::get('/create', 'RoleController@create')->name('RoleController@create');
            Route::post('/store', 'RoleController@store')->name('RoleController@store');
            Route::get('/show', 'RoleController@show')->name('RoleController@show');
            Route::get('/{model}/edit', 'RoleController@edit')->name('RoleController@edit');
            Route::post('/update', 'RoleController@update')->name('RoleController@update');
            Route::post('/destroy/{model}', 'RoleController@destroy')->name('RoleController@destroy');
            Route::post('/mass_destroy', 'RoleController@massDestroy')->name('RoleController@massDestroy');
            Route::post('/restore', 'RoleController@restore')->name('RoleController@restore');
            Route::post('/mass_restore', 'RoleController@massRestore')->name('RoleController@massRestore');
        });
    });
});
