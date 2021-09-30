<?php

use App\Http\Center\Helper\PluginHelper;

if (PluginHelper::pluginCanUse('ViewLog')) {
    // View Log
    Route::get('viewLog', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
}
