<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\EasyManager\Controllers\AccountController;
use Plugins\EasyManager\Controllers\CacheController;
use Plugins\EasyManager\Controllers\CmdWordController;
use Plugins\EasyManager\Controllers\CommentController;
use Plugins\EasyManager\Controllers\FileController;
use Plugins\EasyManager\Controllers\GroupController;
use Plugins\EasyManager\Controllers\HashtagController;
use Plugins\EasyManager\Controllers\HomeController;
use Plugins\EasyManager\Controllers\PostController;
use Plugins\EasyManager\Controllers\UserController;

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

Route::prefix('easy-manager')->name('easy-manager.')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::resource('account', AccountController::class)->only([
        'index', 'update', 'destroy',
    ]);
    Route::get('account/{accountId}/connects', [AccountController::class, 'connects'])->name('account.connects');

    Route::resource('user', UserController::class)->only([
        'index', 'update', 'destroy',
    ]);

    Route::post('user-role/{uid}', [UserController::class, 'storeRole'])->name('user.store.role');
    Route::delete('user-role/{id}', [UserController::class, 'deleteRole'])->name('user.delete.role');

    Route::resource('group', GroupController::class)->only([
        'index', 'update', 'destroy',
    ]);
    Route::get('group/permissions/{groupId}', [GroupController::class, 'groupEditPermissions'])->name('group.edit.permissions');
    Route::put('group/permissions/{groupId}', [GroupController::class, 'groupUpdatePermissions'])->name('group.update.permissions');

    Route::resource('hashtag', HashtagController::class)->only([
        'index', 'update', 'destroy',
    ]);

    Route::resource('post', PostController::class)->only([
        'index', 'update', 'destroy',
    ]);

    Route::resource('comment', CommentController::class)->only([
        'index', 'update', 'destroy',
    ]);

    Route::resource('file', FileController::class)->only([
        'index', 'update', 'destroy',
    ]);

    Route::prefix('cache')->name('cache.')->group(function () {
        Route::get('/', [CacheController::class, 'index'])->name('index');
        Route::delete('destroy', [CacheController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('cmd-word')->name('cmd-word.')->group(function () {
        Route::get('/', [CmdWordController::class, 'index'])->name('index');
    });
});
