<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\App\Http\Controllers\Api\ClientAuthController;
use Modules\Client\App\Http\Controllers\Api\ClientController;
use Modules\Client\App\Http\Controllers\Api\AddressController;
use Modules\Client\App\Http\Controllers\Api\PhoneVerificationController;
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
    'prefix' => 'client'
], function ($router) {
    Route::post('authenticate', [ClientAuthController::class, 'loginOrRegister']);
    Route::group(['prefix' => 'auth'], function ($router) {
        // Route::post('login', [ClientAuthController::class, 'login']);
        // Route::post('register', [ClientAuthController::class, 'register']);
        Route::post('logout', [ClientAuthController::class, 'logout']);
        Route::post('verify', [ClientAuthController::class, 'verifyOtp']);
        Route::post('refresh', [ClientAuthController::class, 'refresh']);
        Route::post('me', [ClientAuthController::class, 'me']);
        // Route::post('check-phone-exists', [ClientAuthController::class, 'checkPhoneExists']);
    });
    Route::post('change-password', [ClientController::class, 'changePassword']);
    Route::post('update-profile', [ClientController::class, 'updateProfile']);
});