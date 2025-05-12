<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Service\App\Http\Controllers\Api\ServiceController;
use Modules\Service\App\Http\Controllers\Api\ServiceAdminController;
use Modules\Service\App\Http\Controllers\Api\ServiceProviderController;

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
Route::group(['prefix' => 'provider'], function ($router) {
    Route::apiResource('services', ServiceProviderController::class)->only(['index', 'store', 'destroy', 'show']);
    Route::post('services/{service}', [ServiceProviderController::class, 'update']);
});

Route::group(['prefix' => 'admin'], function ($router) {
    Route::apiResource('services', ServiceAdminController::class)->only(['index', 'destroy', 'show']);
    Route::post('services/{service}', [ServiceAdminController::class, 'update']);
    Route::post('services/{service}/toggle-activate', [ServiceAdminController::class, 'toggleActivate']);
});

Route::get('services', [ServiceController::class, 'index']);