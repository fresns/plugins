<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\WeChatLogin\Http\Controllers\AdminController;
use Plugins\WeChatLogin\Http\Controllers\WebController;
use Plugins\WeChatLogin\Http\Middleware\WeChatConfig;

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
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('update', [AdminController::class, 'update'])->name('update');
});

Route::middleware('web')->middleware(WeChatConfig::class)->group(function () {
    Route::get('index', [WebController::class, 'index'])->name('index');
    Route::get('sign-in', [WebController::class, 'signIn'])->name('sign.in');
    Route::get('web-sign', [WebController::class, 'webSign'])->name('web.sign');
    Route::get('auth-callback', [WebController::class, 'authCallback'])->name('auth.callback');
    Route::get('create-account', [WebController::class, 'createAccount'])->name('create.account');

    // 账号设置页的绑定和解绑
    Route::get('connect/add', [WebController::class, 'connectAdd'])->name('connect.add');
    Route::get('connect/add-callback', [WebController::class, 'connectAddCallback'])->name('connect.add.callback');
    Route::get('connect/disconnect', [WebController::class, 'connectDisconnect'])->name('connect.disconnect');
    Route::get('connect/disconnect-result', [WebController::class, 'connectDisconnectResult'])->name('connect.disconnect.result');
});
