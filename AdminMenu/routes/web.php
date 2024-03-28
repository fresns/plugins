<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\AdminMenu\Http\Controllers\WebController;
use Plugins\AdminMenu\Http\Middleware\CheckAccess;
use Plugins\AdminMenu\Http\Middleware\CheckAuth;

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

Route::prefix('admin-menu')->name('admin-menu.')->group(function () {
    Route::get('/', [WebController::class, 'index'])->middleware(CheckAccess::class)->name('index');

    Route::prefix('api')->name('api.')->middleware(CheckAuth::class)->group(function () {
        Route::get('groups', [WebController::class, 'groups'])->name('groups');

        Route::patch('edit-user', [WebController::class, 'editUser'])->name('edit.user');
        Route::patch('edit-post', [WebController::class, 'editPost'])->name('edit.post');
        Route::patch('edit-comment', [WebController::class, 'editComment'])->name('edit.comment');

        Route::delete('delete-user', [WebController::class, 'deleteUser'])->name('delete.user');
        Route::delete('delete-post', [WebController::class, 'deletePost'])->name('delete.post');
        Route::delete('delete-comment', [WebController::class, 'deleteComment'])->name('delete.comment');
    });
});
