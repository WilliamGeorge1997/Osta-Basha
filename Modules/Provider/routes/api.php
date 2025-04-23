<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Provider\App\Http\Controllers\Api\ProviderController;
use Modules\Provider\App\Http\Controllers\Api\ProviderAuthController;

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
    'prefix' => 'provider'
], function ($router) {
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login', [ProviderAuthController::class, 'login']);
        Route::post('logout', [ProviderAuthController::class, 'logout']);
        Route::post('register', [ProviderAuthController::class, 'register']);
        Route::post('verify', [ProviderAuthController::class, 'verifyOtp']);
        Route::post('refresh', [ProviderAuthController::class, 'refresh']);
        Route::post('me', [ProviderAuthController::class, 'me']);
        Route::post('check-phone-exists', [ProviderAuthController::class, 'checkPhoneExists']);
    });
    Route::post('change-password', [ProviderController::class, 'changePassword']);
    Route::post('update-profile', [ProviderController::class, 'updateProfile']);
});
