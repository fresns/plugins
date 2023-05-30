<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\TitleIcons\Controllers\AdminController;
use Plugins\TitleIcons\Controllers\WebController;

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

Route::prefix('title-icons')->name('title-icons.')->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('index');
    Route::get('/edit-post-title-icon', [WebController::class, 'editPostTitleIcon'])->name('edit.post.title.icon');
    Route::get('/edit-comment-title-icon', [WebController::class, 'editCommentTitleIcon'])->name('edit.comment.title.icon');

    Route::middleware(['panel', 'panelAuth'])->group(function () {
        Route::resource('admin', AdminController::class)->only([
            'index', 'store', 'update', 'destroy',
        ]);
    });
});
