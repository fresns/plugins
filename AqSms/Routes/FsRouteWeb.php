<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Http\Center\Helper\PluginHelper;

// 判断插件是否开启
if (PluginHelper::pluginCanUse('AqSms')) {
    Route::group(['prefix' => 'aqsms', 'middleware' => ['web', 'auth'], 'namespace' => '\App\Plugins\AqSms\Controllers'], function () {
        // 设置页
        Route::get('/setting', 'ControllerWeb@setting')->name('aqsms.setting');
        // 设置插件配置项
        Route::post('/saveSetting', 'ControllerApi@saveSetting')->name('aqsms.saveSetting');
    });
}
