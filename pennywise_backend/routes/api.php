<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Load route names from config
$routeNames = config('routes');

Route::controller(AuthController::class)->group(function () use ($routeNames) {
    Route::post('/register', 'register')->name($routeNames['register']);
    Route::post('/login', 'login')->name($routeNames['login']);
    Route::post('/reset/otp', 'resetOtp')->name($routeNames['reset_otp']);
    Route::post('/reset/password', 'resetPassword')->name($routeNames['reset_password']);

    Route::middleware('auth:sanctum')->group(function () use ($routeNames) {
        Route::post('/otp', 'otp')->name($routeNames['otp']);
        Route::post('/verify', 'verify')->name($routeNames['verify']);
    });
});
