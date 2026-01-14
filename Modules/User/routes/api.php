<?php

use Illuminate\Support\Facades\Route;
use Modules\User\App\Http\Controllers\Api\UserController;
use Modules\User\App\Http\Controllers\Api\UserAuthController;
use Modules\User\App\Http\Controllers\Api\UserAdminController;
use Modules\User\App\Http\Controllers\Api\NotificationController;

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
    Route::post('users/{user}/toggle-activate', [UserAdminController::class, 'toggleActivate']);
});

Route::group([
    'prefix' => 'user'
], function ($router) {
    Route::group(['prefix' => 'auth'], function ($router) {
        Route::post('login-or-register', [UserAuthController::class, 'loginOrRegister']);
        Route::post('choose-user-type', [UserAuthController::class, 'chooseUserType']);
        Route::post('complete-registration', [UserAuthController::class, 'completeRegistration']);
        Route::post('choose-subcategories', [UserAuthController::class, 'chooseSubCategories']);
        Route::post('verify', [UserAuthController::class, 'verifyOtp']);
        Route::post('logout', [UserAuthController::class, 'logout']);
        Route::post('refresh', [UserAuthController::class, 'refresh']);
        Route::post('me', [UserAuthController::class, 'me']);
    });
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('update-profile', [UserController::class, 'updateProfile']);
    Route::post('delete-image/{id}', [UserController::class, 'deleteImage']);
    Route::get('search', [UserController::class, 'search']);
    Route::post('toggle-available', [UserController::class, 'toggleAvailable']);
    Route::get('received-contacts', [UserController::class, 'receivedContacts']);
    Route::post('update-location', [UserController::class, 'updateLocation']);
    Route::post('delete-subcategories', [UserController::class, 'deleteSubCategories']);

    //Forget Password
    Route::post('forget-password', [UserAuthController::class, 'forgetPassword']);
    Route::post('verify-forget-password', [UserAuthController::class, 'verifyForgetPassword']);
    Route::post('new-password', [UserAuthController::class, 'newPassword']);
    Route::post('resend-otp', [UserAuthController::class, 'resendOtp']);
});

Route::group([
    'prefix' => 'notification',
], function ($router) {
    Route::get('all', [NotificationController::class, 'index']);
    Route::post('read', [NotificationController::class, 'readNotification']);
    Route::post('allow_notification', [NotificationController::class, 'allow_notification']);
    Route::get('unReadNotificationsCount', [NotificationController::class, 'unReadNotificationsCount']);
});
