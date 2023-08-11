<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;
use Plugins\ConfigManager\Controllers\WebController;

Route::middleware(['panel', 'panelAuth'])->group(function () {
    Route::post('config-manager', [WebController::class, 'store'])->name('api.config-manager.index');
    Route::put('config-manager', [WebController::class, 'store']);
});
