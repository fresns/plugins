<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Http\Center\Helper\PluginHelper;

if (PluginHelper::pluginCanUse('ViewLog')) {
    // View Log
    Route::get('viewLog', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}
