<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\App\Http\Controllers\Api\RateController;
use Modules\Client\App\Http\Controllers\Api\ClientController;
use Modules\Client\App\Http\Controllers\Api\CommentController;
use Modules\Client\App\Http\Controllers\Api\RateAdminController;
use Modules\Client\App\Http\Controllers\Api\ClientAdminController;
use Modules\Client\App\Http\Controllers\Api\CommentAdminController;
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
    Route::post('rate/{clientContact}', [RateController::class, 'store']);
    Route::put('rate/{clientContact}', [RateController::class, 'update']);
    Route::delete('rate/{clientContact}', [RateController::class, 'destroy']);
    Route::get('contact-list', [ClientController::class, 'clientContactList']);
});