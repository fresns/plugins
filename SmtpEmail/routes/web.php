<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\SmtpEmail\Http\Controllers\WebController;

Route::group(['prefix' => 'smtp-email', 'middleware' => ['panelAuth']], function () {
    Route::get('/settings', [WebController::class, 'settings'])->name('fresnsemail.settings.show');
    Route::post('/settings', [WebController::class, 'postSettings'])->name('fresnsemail.settings.store');
    Route::post('/test', [WebController::class, 'sendTest'])->name('fresnsemail.settings.test');
});
