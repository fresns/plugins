<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\AmazonSimpleEmailService\Http\Controllers\WebController;

Route::prefix('amazon-ses')->name('amazon-ses.')->middleware('panel', 'panelAuth')->group(function () {
    Route::get('/settings', [WebController::class, 'settings'])->name('settings.show');
    Route::post('/settings', [WebController::class, 'postSettings'])->name('settings.store');
    Route::post('/test', [WebController::class, 'sendTest'])->name('settings.test');
});
