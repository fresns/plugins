<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\Plugins\Placeholder\Http\Controllers'], function () {
    Route::get('/index', 'WebController@index')->name('placeholder.web.index');
});
