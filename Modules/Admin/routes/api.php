<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\App\Http\Controllers\Api\AdminController;
use Modules\Admin\App\Http\Controllers\Api\AdminAuthController;
use Modules\Admin\App\Http\Controllers\Api\NotificationController;
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
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::post('refresh', [AdminAuthController::class, 'refresh']);
        Route::post('me', [AdminAuthController::class, 'me']);
    });
    Route::post('change-password', [AdminController::class, 'changePassword']);
    Route::post('update-profile', [AdminController::class, 'updateProfile']);

    Route::group([
        'prefix' => 'notification',
    ], function ($router) {
        Route::get('all', [NotificationController::class, 'index']);
        Route::post('read', [NotificationController::class, 'readNotification']);
        Route::post('allow_notification', [NotificationController::class, 'allow_notification']);
        Route::get('unReadNotificationsCount', [NotificationController::class, 'unReadNotificationsCount']);
        Route::post('create', [NotificationController::class, 'createNotification']);
        Route::get('test-query', [NotificationController::class, 'testQuery']);
    });
});



