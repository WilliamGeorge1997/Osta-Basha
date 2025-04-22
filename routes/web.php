<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\App\Http\Controllers\Api\ClientAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/auth/google', [ClientAuthController::class, 'googleLogin'])->name('client.auth.google');
Route::get('/auth/google-callback', [ClientAuthController::class, 'googleCallback'])->name('client.auth.google.callback');
