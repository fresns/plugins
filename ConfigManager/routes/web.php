<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\ConfigManager\Controllers\WebController;

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

Route::prefix('config-manager')->name('config-manager.')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('index');

    Route::delete('/', [WebController::class, 'delete'])->name('config-manager.delete');
});
