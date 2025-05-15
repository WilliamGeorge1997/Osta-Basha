<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\App\Http\Controllers\Api\ClientController;
use Modules\Client\App\Http\Controllers\Api\ClientAdminController;
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

Route::group([
    'prefix' => 'admin'
], function ($router) {
    Route::apiResource('clients', ClientAdminController::class)->only(['index']);
});
Route::group([
    'prefix' => 'client'
], function ($router) {
    Route::post('contact', [ClientController::class, 'clientContact']);
});