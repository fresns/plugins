<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\ConfigManager\Controllers\WebController;

Route::prefix('config-manager')->name('config-manager.')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [WebController::class, 'index'])->name('index');
    Route::post('item', [WebController::class, 'update'])->name('update');
    Route::delete('item', [WebController::class, 'delete'])->name('delete');
});
