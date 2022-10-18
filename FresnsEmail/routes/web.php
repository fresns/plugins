<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\FresnsEmail\Http\Controllers;

Route::group(['prefix' => 'fresns-email', 'middleware' => ['panelAuth']], function () {
    Route::get('/settings', [Controllers\WebController::class, 'settings'])->name('fresnsemail.settings.show');
    Route::post('/settings', [Controllers\WebController::class, 'postSettings'])->name('fresnsemail.settings.store');
    Route::post('/test', [Controllers\WebController::class, 'sendTest'])->name('fresnsemail.settings.test');
});
