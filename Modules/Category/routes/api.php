<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\App\Http\Controllers\Api\CategoryController;
use Modules\Category\App\Http\Controllers\Api\SubCategoryController;
use Modules\Category\App\Http\Controllers\Api\CategoryAdminController;
use Modules\Category\App\Http\Controllers\Api\SubCategoryAdminController;
use Modules\Category\App\Http\Controllers\Api\CategoryLocalizationAdminController;
use Modules\Category\App\Http\Controllers\Api\SubCategoryLocalizationAdminController;


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
    Route::apiResource('categories', CategoryAdminController::class)->except('update');
    Route::post('categories/{category}', [CategoryAdminController::class, 'update']);
    Route::get('categories/{category}/sub-categories', [CategoryAdminController::class, 'subCategories']);
    Route::post('categories/{category}/toggle-activate', [CategoryAdminController::class, 'toggleActivate']);


    Route::apiResource('sub-categories', SubCategoryAdminController::class)->except('update');
    Route::post('sub-categories/{subCategory}', [SubCategoryAdminController::class, 'update']);
    Route::post('sub-categories/{subCategory}/toggle-activate', [SubCategoryAdminController::class, 'toggleActivate']);

    Route::apiResource('category-localizations', CategoryLocalizationAdminController::class)->except('update');
    Route::post('category-localizations/{categoryLocalization}', [CategoryLocalizationAdminController::class, 'update']);
    Route::apiResource('sub-category-localizations', SubCategoryLocalizationAdminController::class)->except('update');
    Route::post('sub-category-localizations/{subCategoryLocalization}', [SubCategoryLocalizationAdminController::class, 'update']);
});

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}/sub-categories', [SubCategoryController::class, 'index']);
