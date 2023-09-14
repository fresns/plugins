<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\WeChatLogin\Http\Controllers\ApiController;
use Plugins\WeChatLogin\Http\Middleware\CheckHeaders;

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

Route::prefix('wechat-login')->name('api.')->group(function () {
    // 公共接口
    Route::prefix('common')->name('common.')->group(function () {
        Route::get('callback', [ApiController::class, 'callback'])->name('callback');
        Route::post('recallback', [ApiController::class, 'recallback'])->name('recallback');
    });

    // 小程序
    Route::prefix('mini-program')->name('mini-program.')->middleware(CheckHeaders::class)->group(function () {
        Route::post('oauth', [ApiController::class, 'miniProgramOauth'])->name('oauth');
        Route::post('oauth-website', [ApiController::class, 'miniProgramOauthWebsite'])->name('oauth.website');
    });

    // 开放平台
    Route::prefix('open-platform')->name('open-platform.')->middleware(CheckHeaders::class)->group(function () {
        Route::post('oauth', [ApiController::class, 'openPlatformOauth'])->name('oauth');
    });

    // 多端应用
    Route::prefix('mini-app')->name('mini-app.')->middleware(CheckHeaders::class)->group(function () {
        Route::post('oauth-apple', [ApiController::class, 'miniAppOauthApple'])->name('oauth.apple');
    });
});
