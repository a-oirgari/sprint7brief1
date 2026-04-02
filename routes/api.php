<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// OAuth routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| Protected routes (Sanctum token required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/me',          [ProfileController::class, 'show']);
    Route::put('/me',          [ProfileController::class, 'update']);
    Route::put('/me/password', [ProfileController::class, 'updatePassword']);
    Route::delete('/me',       [ProfileController::class, 'destroy']);
});