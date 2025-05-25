<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Country\App\Http\Controllers\Api\CountryController;
use Modules\Country\App\Http\Controllers\Api\CountryAdminController;

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
    Route::apiResource('countries', CountryAdminController::class)->except(['update']);
    Route::post('countries/{country}', [CountryAdminController::class, 'update'])->name('country.update');
    Route::post('countries/{country}/toggle-activate', [CountryAdminController::class, 'toggleActivate'])->name('country.toggle-activate');
});

Route::get('countries', [CountryController::class, 'index']);