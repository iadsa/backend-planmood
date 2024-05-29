<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\JurnalController;

// Handle OPTIONS requests for CORS
Route::options('/{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');

// Public routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes for authentication
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Protected routes for API
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    Route::get('/posts', [JurnalController::class, 'index']);
    Route::get('/posts/{id}', [JurnalController::class, 'show']);
    Route::post('/posts', [JurnalController::class, 'store']);
    Route::put('/posts/{id}', [JurnalController::class, 'update']);
    Route::delete('/posts/{id}', [JurnalController::class, 'destroy']);
});

// Fallback route for undefined routes
Route::fallback(function () {
    return response()->json(['message' => 'Route tidak ditemukan'], 404);
});
