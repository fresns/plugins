<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Support\Facades\Route;
use Plugins\Cloudinary\Http\Controllers as Api;

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

Route::middleware('api')->group(function () {
    Route::get('cloudinary/signdata', [Api\WebController::class, 'getSignData'])->name('cloudinary.signdata');
    Route::post('cloudinary/upload-fileinfo', [Api\WebController::class, 'uploadFileInfo'])->name('qiniu.upload.fileinfo');
});
