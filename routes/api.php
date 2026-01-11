<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MasterDataController;


// Auth APIs
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/create-password', [AuthController::class, 'createPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);


    Route::get('/states/{state}/cities', [MasterDataController::class, 'citiesByState']);
    Route::get('/master-data', [MasterDataController::class, 'index']);

});
