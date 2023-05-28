<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\PortalEditor\Controllers\EditController;

Route::prefix('portal-editor')->name('portal-editor.')->middleware(['panel', 'panelAuth'])->group(function () {
    Route::get('/', [EditController::class, 'index'])->name('index');
    Route::get('edit/{id}/{langTag}', [EditController::class, 'edit'])->name('edit');
    Route::put('update/{id}/{langTag}', [EditController::class, 'update'])->name('update');
    Route::put('update-auto', [EditController::class, 'updateAuto'])->name('update.auto');
    Route::post('update-now', [EditController::class, 'updateNow'])->name('update.now');
});
