<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Plugins\EasySms\Http\Controllers as ApiController;

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

Route::prefix('EasySms')->middleware(['api', 'auth'])->group(function() {
    Route::get('/', [ApiController\EasySmsController::class, 'index']);
});
