<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

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
Route::post('send_invitation', [TestController::class , 'sendEmail']);
Route::post('register', [TestController::class , 'registerUser']);
Route::post('login', [TestController::class , 'loginUser']);
Route::post('verify', [TestController::class , 'verifyUser']);
Route::post('update', [TestController::class , 'updateUser'])->middleware('auth');
