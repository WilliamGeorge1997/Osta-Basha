<?php

use Illuminate\Support\Facades\Route;
use Modules\Common\App\Http\Controllers\Api\CommonController;
use Modules\Common\App\Http\Controllers\Api\SettingController;

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


Route::post('contact', [CommonController::class, 'contact']);
Route::apiResource('settings', SettingController::class)->only(['index']);
Route::post('settings', [SettingController::class, 'update']);