<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\QiNiu\Http\Controllers;

Route::group(['prefix' => 'qiniu'], function () {
    Route::post('/uploadCallback', [Controllers\QiNiuControllerApi::class, 'uploadCallback'])->name('qiniu.file.uploadCallback');
    Route::get('/getToken', [Controllers\QiNiuControllerApi::class, 'getToken'])->name('qiniu.file.getToken');
    Route::any('/trans/notify', [Controllers\QiNiuControllerTrans::class, 'transNotify'])->name('qiniu.file.transNotify');
});
