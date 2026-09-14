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

Route::post('/cookies',             'FrontendController@cookies')->name('FrontendController@cookies');

Route::group([
    'prefix'        => LaravelLocalization::setLocale(),
    'middleware'    => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function() {
    Route::get('/clear-cache', 'HomeController@clearCache')->name('cache.clear');

    Route::get('/', function() {
        return view('frontend::soon');
    })->name('temp');

    Route::get('/', 'HomeController@index')->name('index');



    Route::group(['prefix' => 'opportunities', 'as' => 'OpportunityController@'], function () {
        Route::get('/{type}/{slug}',        'OpportunityController@single')->name('single');
        Route::get('/{opportunity_classification}/{contract}/{city}/{area?}', 'OpportunityController@filter')->name('filter');
        Route::post('process_filter',       'OpportunityController@prepareFilter')->name('prepareFilter');

    });
    

    Route::group(['prefix' => ''], function () {
        Route::get('/landing-page/{slug}',  'LandingPageController@details')->name('LandingPageController@details');
        Route::post('process_filter',       'ListingController@prepareFilter')->name('ListingController@prepareFilter');
        Route::get('/search',               'ListingController@search')->name('ListingController@search');
        Route::post('set_currency',         'FrontendController@setCurrency')->name('FrontendController@setCurrency');
        Route::get('/services',             'ListingController@services')->name('ListingController@services');
        Route::get('/articles/{slug?}',     'ListingController@articles')->name('ListingController@articles');
        Route::get('/installments-by-payments/{id}', 'ListingController@getInstallmentsByPayment')->name('ListingController@installments');
        Route::get('/faqs',                 'ListingController@faqs')->name('ListingController@faqs');
        Route::get('/{slug}',               'ListingController@getContentBySlug')->name('ListingController@getContentBySlug');
        Route::get('/{type}/{slug}',        'PropertyController@single')->name('PropertyController@single');
    });

    Route::group(['prefix' => 'contact-us', 'as' => 'ContactController@'], function () {
        Route::get('/'              , 'ContactController@index')->name('index');
        Route::post('/store'        , 'ContactController@store')->name('store');
        Route::post('/store-visit'  , 'ContactController@storeVisit')->name('storeVisit');
        Route::post('/subscribe'    , 'ContactController@subscribe')->name('subscribe');
        Route::post('/store-inner'  , 'ContactController@storeInner')->name('storeInner');
    });

    Route::get('listing/regions-by-city-id/{id}', 'ListingController@getRegionsByCityId')->name('ListingController@regions');
    Route::get('{property_classification}/{contract}/{city}/{area?}', 'ListingController@filter')->name('ListingController@filter');



});
