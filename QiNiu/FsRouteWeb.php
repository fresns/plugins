<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Http\Center\Helper\PluginHelper;

// 判断插件是否开启
if (PluginHelper::pluginCanUse('QiNiu')) {
    Route::group(['prefix' => 'qiniu', 'namespace' => '\App\Plugins\QiNiu'], function () {
        Route::get('/upload', 'QiNiuControllerWeb@upload')->name('qiniu.file.upload');
    });
}
