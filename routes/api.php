<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\JurnalController;
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

// Routes untuk jurnal 
Route::get('/posts', [JurnalController::class, 'index']);
Route::post('/posts', [JurnalController::class, 'store']);
Route::get('/posts/{id}', [JurnalController::class, 'show']);
Route::put('/posts/{id}', [JurnalController::class, 'update']);
Route::delete('/posts/{id}', [JurnalController::class, 'destroy']);

// Fallback route untuk menangani route yang tidak ditemukan
Route::fallback(function () {
    return response()->json(['message' => 'Route tidak ditemukan'], 404);
});
