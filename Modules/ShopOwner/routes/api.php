<?php

use Illuminate\Support\Facades\Route;
use Modules\ShopOwner\App\Http\Controllers\Api\ShopOwnerController;
use Modules\ShopOwner\App\Http\Controllers\Api\ShopOwnerAdminController;

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
    Route::apiResource('shop-owners', ShopOwnerAdminController::class)->only(['index']);
});
Route::apiResource('shop-owners', ShopOwnerController::class)->only(['index']);