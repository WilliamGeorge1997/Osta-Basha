<?php

use Illuminate\Support\Facades\Route;
use Modules\ShopOwner\App\Http\Controllers\ShopOwnerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('shopowner', ShopOwnerController::class)->names('shopowner');
});
