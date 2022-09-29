<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\EasySms\Http\Controllers as Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('easy-sms')->middleware(['web', 'panelAuth'])->group(function () {
    Route::get('/setting', [Controller\EasySmsController::class, 'setting'])->name('EasySms.setting');
    Route::post('/saveSetting', [Controller\EasySmsController::class, 'saveSetting'])->name('EasySms.saveSetting');
});
