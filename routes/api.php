<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [\App\Http\Controllers\API\AuthController::class,'login']);
    Route::post('logout', [\App\Http\Controllers\API\AuthController::class,'logout']);
    Route::post('refresh', [\App\Http\Controllers\API\AuthController::class,'refresh']);
    Route::post('getMe', [\App\Http\Controllers\API\AuthController::class,'getMe']);
});
