<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Http\Center\Helper\PluginHelper;

// Determine if the plugin is available
if (PluginHelper::pluginCanUse('FresnsEmail')) {
    Route::group(['prefix' => 'fresnsemail', 'namespace' => '\App\Plugins\FresnsEmail\Controllers'], function () {
        Route::get('/settings', 'WebController@settings')->name('fresnsemail.settings.show');
        Route::post('/settings', 'WebController@postSettings')->name('fresnsemail.settings.store');
        Route::any('/test', 'WebController@sendTest')->name('fresnsemail.settings.test');
    });
}
