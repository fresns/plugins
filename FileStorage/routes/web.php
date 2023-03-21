<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\FileStorage\Http\Controllers\AdminController;

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

Route::name('admin.')->prefix('admin')->middleware(['web', 'panelAuth'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');

    Route::get('image', [AdminController::class, 'adminImage'])->name('image');
    Route::get('video', [AdminController::class, 'adminVideo'])->name('video');
    Route::get('audio', [AdminController::class, 'adminAudio'])->name('audio');
    Route::get('document', [AdminController::class, 'adminDocument'])->name('document');
    Route::get('test', [AdminController::class, 'adminTest'])->name('test');

    Route::put('update', [AdminController::class, 'update'])->name('update');
    Route::post('upload-file', [AdminController::class, 'uploadFile'])->name('upload.file');
    Route::delete('delete-file/{type}/{fid}', [AdminController::class, 'deleteFile'])->name('delete.file');
});
