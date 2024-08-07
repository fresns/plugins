<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\GitHubSignIn\Http\Controllers\AdminController;
use Plugins\GitHubSignIn\Http\Controllers\WebController;
use Plugins\GitHubSignIn\Http\Middleware\WebConfig;

Route::name('github-signin.')->prefix('github-signin')->group(function () {
    Route::name('admin.')->prefix('admin')->middleware(['panel', 'panelAuth'])->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::put('update', [AdminController::class, 'update'])->name('update');
    });

    Route::middleware('web')->middleware(WebConfig::class)->group(function () {
        Route::get('index', [WebController::class, 'index'])->name('index');

        Route::name('connect.')->prefix('connect')->group(function () {
            Route::get('sign-in', [WebController::class, 'signIn'])->name('sign-in');
            Route::get('add', [WebController::class, 'add'])->name('add');
            Route::get('disconnect', [WebController::class, 'disconnect'])->name('disconnect');
            Route::get('disconnect-result', [WebController::class, 'disconnectResult'])->name('disconnect.result');
        });

        Route::get('auth-callback', [WebController::class, 'authCallback'])->name('auth.callback');

        Route::get('create-account', [WebController::class, 'createAccount'])->name('create.account');
    });
});
