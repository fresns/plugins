<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\SharePoster\Http\Controllers\AdminController;

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

Route::name('admin.')->prefix('admin')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index'); // user
    Route::get('group', [AdminController::class, 'group'])->name('group');
    Route::get('hashtag', [AdminController::class, 'hashtag'])->name('hashtag');
    Route::get('geotag', [AdminController::class, 'geotag'])->name('geotag');
    Route::get('post', [AdminController::class, 'post'])->name('post');
    Route::get('comment', [AdminController::class, 'comment'])->name('comment');
    Route::get('font', [AdminController::class, 'font'])->name('font');
    Route::put('update', [AdminController::class, 'update'])->name('update');
});
