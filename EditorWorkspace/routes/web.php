<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\EditorWorkspace\Controllers\AdminController;
use Plugins\EditorWorkspace\Controllers\WorkController;

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

Route::name('editor-workspace.')->group(function () {
    // admin
    Route::prefix('admin')->name('admin.')->middleware(['panel', 'panelAuth'])->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('users', [AdminController::class, 'users'])->name('users');
        Route::post('account-add', [AdminController::class, 'accountAdd'])->name('account.add');
        Route::delete('account-remove', [AdminController::class, 'accountRemove'])->name('account.remove');
        Route::post('account-generate', [AdminController::class, 'accountGenerate'])->name('account.generate');
        Route::post('user-generate', [AdminController::class, 'userGenerate'])->name('user.generate');
    });

    // work
    Route::prefix('work')->name('work.')->group(function () {
        Route::get('/', [WorkController::class, 'index'])->name('index');
        Route::get('editor', [WorkController::class, 'editor'])->name('editor');
        Route::get('groups/{gid}', [WorkController::class, 'groups'])->name('groups');
        Route::post('quick-publish', [WorkController::class, 'quickPublish'])->name('quick.publish');
    });
});
