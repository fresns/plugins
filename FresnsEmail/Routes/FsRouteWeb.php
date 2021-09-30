<?php

use App\Http\Center\Helper\PluginHelper;

// Determine if the plugin is available
if (PluginHelper::pluginCanUse('FresnsEmail')) {
    Route::group(['prefix' => 'fresnsemail', 'namespace' => '\App\Plugins\FresnsEmail\Controllers'], function () {
        Route::get('/settings', 'WebController@settings')->name('fresnsemail.settings.show');
        Route::post('/settings', 'WebController@postSettings')->name('fresnsemail.settings.store');
        Route::get('/test', 'WebController@sendTest')->name('fresnsemail.settings.test');
    });
}
