<?php

use Illuminate\Support\Facades\Route;
use Modules\Common\App\Http\Controllers\Api\PageController;
use Modules\Common\App\Http\Controllers\Api\CommonController;
use Modules\Common\App\Http\Controllers\Api\SliderController;
use Modules\Common\App\Http\Controllers\Api\SettingController;
use Modules\Common\App\Http\Controllers\Api\PageAdminController;
use Modules\Common\App\Http\Controllers\Api\SliderAdminController;

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


Route::post('contact', [CommonController::class, 'contact']);
Route::apiResource('settings', SettingController::class)->only(['index']);
Route::post('settings', [SettingController::class, 'update']);


// Slider Routes
Route::group(['prefix' => 'admin'], function () {
    Route::apiResource('sliders', SliderAdminController::class)->only(['index', 'store', 'destroy']);
    Route::post('sliders/{slider}', [SliderAdminController::class, 'update']);
    Route::post('sliders/{slider}/toggle-activate', [SliderAdminController::class, 'toggleActivate']);


    Route::apiResource('pages', PageAdminController::class)->only(['index', 'store', 'destroy']);
    Route::post('pages/{page}', [PageAdminController::class, 'update']);
});

Route::get('pages/find-page', [PageController::class, 'findPage']);
Route::get('currencies', [CommonController::class, 'currencies']);
Route::get('sliders', [SliderController::class, 'index']);