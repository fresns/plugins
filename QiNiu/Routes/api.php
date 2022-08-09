<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plugins\QiNiu\Http\Controllers\QiNiuApiController;

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

Route::middleware('auth:api')->get('/qiniu', function (Request $request) {
    return $request->user();
});

Route::middleware('api')->post('/qiniu/transcoding/{uuid}', [QiNiuApiController::class, 'callback'])->name('qiniu.transcoding.callback');