<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\QiNiu\Http\Controllers;

Route::group(['prefix' => 'qiniu'], function () {
    Route::get('/upload', [Controllers\QiNiuControllerWeb::class, 'upload'])->name('qiniu.file.upload');
});
