<?php

Route::group([
    'prefix'        => LaravelLocalization::setLocale(),
    'middleware'    => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function()
{
	Route::group([ 'prefix'    => 'admin', 'namespace' => 'Admin' ], function() {
        Route::group([ 'prefix'    => 'projects' ], function() {
            Route::get('/',                         'ProjectController@index')->name('ProjectController@index');
            Route::get('/data',                     'ProjectController@data')->name('ProjectController@data');
            Route::get('/create',                   'ProjectController@create')->name('ProjectController@create');
            Route::post('/store',                   'ProjectController@store')->name('ProjectController@store');
            Route::get('/show',                     'ProjectController@show')->name('ProjectController@show');
            Route::get('/{model?}/edit',            'ProjectController@edit')->name('ProjectController@edit');
            Route::post('/{model}/update',          'ProjectController@update')->name('ProjectController@update');
            Route::post('/destroy/{model}',         'ProjectController@destroy')->name('ProjectController@destroy');
            Route::post('/mass_destroy',            'ProjectController@massDestroy')->name('ProjectController@massDestroy');
            Route::post('/restore',                 'ProjectController@restore')->name('ProjectController@restore');
            Route::post('/mass_restore',            'ProjectController@massRestore')->name('ProjectController@massRestore');
            Route::post('/delete-payment',          'ProjectController@deletePayment')->name('ProjectController@deletePayment');
            Route::post('/delete-price',            'ProjectController@deletePrice')->name('ProjectController@deletePrice');
            Route::post('disable/{model}',          'ProjectController@disable')->name('ProjectController@disable');
            Route::post('enable/{model}',           'ProjectController@enable')->name('ProjectController@enable');
            Route::post('special/{model}',          'ProjectController@special')->name('ProjectController@special');
            Route::post('not_special/{model}',      'ProjectController@notSpecial')->name('ProjectController@notSpecial');
            Route::post('sold/{model}',             'ProjectController@sold')->name('ProjectController@sold');
            Route::post('not_sold/{model}',         'ProjectController@notSold')->name('ProjectController@notSold');
            Route::post('destroy/{model}/{locale}', 'ProjectController@destroyTranslation')->name('ProjectController@destroyTranslation');
            Route::post('copy/{model}',             'ProjectController@copy')->name('ProjectController@copy');
            //
            Route::get('/requests',                 'ProjectController@requests')->name('ProjectController@requests');
            Route::get('/data_requests',            'ProjectController@data_requests')->name('ProjectController@data_requests');
            Route::get('/request_summary',          'ProjectController@request_summary')->name('ProjectController@request_summary');
            //
            Route::get('/properties',               'ProjectController@properties')->name('ProjectController@properties');
            Route::get('/data_properties',          'ProjectController@data_properties')->name('ProjectController@data_properties');
            Route::get('/properties_summary',       'ProjectController@properties_summary')->name('ProjectController@properties_summary');
            Route::get('/show_details/{model}',     'ProjectController@showDetails')->name('ProjectController@showDetails');
        });
    });


    Route::group([ 'prefix'    => 'admin', 'namespace' => 'Admin' ], function() {
        Route::group([ 'prefix'    => 'opportunity' ], function() {
            Route::get('/',                         'OpportunityController@index')->name('OpportunityController@index');
            Route::get('/data',                     'OpportunityController@data')->name('OpportunityController@data');
            Route::get('/create',                   'OpportunityController@create')->name('OpportunityController@create');
            Route::post('/store',                   'OpportunityController@store')->name('OpportunityController@store');
            Route::get('/show',                     'OpportunityController@show')->name('OpportunityController@show');
            Route::get('/{model?}/edit',            'OpportunityController@edit')->name('OpportunityController@edit');
            Route::post('/{model}/update',          'OpportunityController@update')->name('OpportunityController@update');
            Route::post('/destroy/{model}',         'OpportunityController@destroy')->name('OpportunityController@destroy');
            Route::post('/mass_destroy',            'OpportunityController@massDestroy')->name('OpportunityController@massDestroy');
            Route::post('/restore',                 'OpportunityController@restore')->name('OpportunityController@restore');
            Route::post('/mass_restore',            'OpportunityController@massRestore')->name('OpportunityController@massRestore');
            Route::post('/delete-payment',          'OpportunityController@deletePayment')->name('OpportunityController@deletePayment');
            Route::post('/delete-price',            'OpportunityController@deletePrice')->name('OpportunityController@deletePrice');
            Route::post('disable/{model}',          'OpportunityController@disable')->name('OpportunityController@disable');
            Route::post('enable/{model}',           'OpportunityController@enable')->name('OpportunityController@enable');
            Route::post('special/{model}',          'OpportunityController@special')->name('OpportunityController@special');
            Route::post('not_special/{model}',      'OpportunityController@notSpecial')->name('OpportunityController@notSpecial');
            Route::post('sold/{model}',             'OpportunityController@sold')->name('OpportunityController@sold');
            Route::post('not_sold/{model}',         'OpportunityController@notSold')->name('OpportunityController@notSold');
            Route::post('destroy/{model}/{locale}', 'OpportunityController@destroyTranslation')->name('OpportunityController@destroyTranslation');
            Route::post('copy/{model}',             'OpportunityController@copy')->name('OpportunityController@copy');
            //
            Route::get('/requests',                 'OpportunityController@requests')->name('OpportunityController@requests');
            Route::get('/data_requests',            'OpportunityController@data_requests')->name('OpportunityController@data_requests');
            Route::get('/request_summary',          'OpportunityController@request_summary')->name('OpportunityController@request_summary');
            //
            Route::get('/properties',               'OpportunityController@properties')->name('OpportunityController@properties');
            Route::get('/data_properties',          'OpportunityController@data_properties')->name('OpportunityController@data_properties');
            Route::get('/properties_summary',       'OpportunityController@properties_summary')->name('OpportunityController@properties_summary');
            Route::get('/show_details/{model}',     'OpportunityController@showDetails')->name('OpportunityController@showDetails');
        });
    });
});
