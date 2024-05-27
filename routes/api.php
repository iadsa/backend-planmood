<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;

// Menggunakan route closure untuk mendapatkan data user yang terautentikasi
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes untuk autentikasi
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Group middleware untuk route yang membutuhkan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});
