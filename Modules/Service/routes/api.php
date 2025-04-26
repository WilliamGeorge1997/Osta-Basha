<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::apiResource('services', ServiceProviderController::class)->only(['index', 'store', 'destroy', 'show']);
Route::post('services/{service}', [ServiceProviderController::class, 'update']);