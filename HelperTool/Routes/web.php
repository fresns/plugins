<?php

use Illuminate\Support\Facades\Route;
use Plugins\HelperTool\Http\Controllers as ApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('helper-tool')->group(function() {
    Route::get('/', [ApiController\HelperToolController::class, 'index']);
});
