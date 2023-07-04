<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\Placeholder\Http\Controllers\WebController;

Route::get('index', [WebController::class, 'index'])->name('index');
