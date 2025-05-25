<?php

use Illuminate\Support\Facades\Route;
use Modules\Provider\App\Http\Controllers\Api\ProviderController;
use Modules\Provider\App\Http\Controllers\Api\ProviderAdminController;

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
    Route::apiResource('providers', ProviderAdminController::class)->only(['index']);
    Route::post('providers/{user}', [ProviderAdminController::class, 'updateSubscription']);
    Route::post('providers/{user}/toggle-activate', [ProviderAdminController::class, 'toggleActivate']);
});

Route::apiResource('providers', ProviderController::class)->only(['index']);
Route::get('most-contacted-providers', [ProviderController::class, 'mostContactedProviders']);
Route::get('related-providers/{user_id}', [ProviderController::class, 'relatedProviders']);