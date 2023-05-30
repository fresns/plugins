<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\OnlineDays\Controllers\EditController;

Route::prefix('online-days')->name('online-days.')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [EditController::class, 'index'])->name('index');
    Route::put('update', [EditController::class, 'update'])->name('update');
});
