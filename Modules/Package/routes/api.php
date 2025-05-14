<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Package\App\Http\Controllers\Api\PackageController;
use Modules\Package\App\Http\Controllers\Api\PackageAdminController;

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
Route::group(['prefix' => 'admin'], function () {
    Route::apiResource('packages', PackageAdminController::class)->except('update');
    Route::post('packages/{package}', [PackageAdminController::class, 'update']);
    Route::post('packages/{package}/toggle-activate', [PackageAdminController::class, 'toggleActivate']);
});
Route::get('packages', [PackageController::class, 'index']);