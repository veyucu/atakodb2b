<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make sure to check it out!
|
*/

// Public routes - Authentication
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes - Require authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::get('me', [AuthController::class, 'me']);
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('find-by-code', [UserController::class, 'findByCode']);
        Route::post('sync', [UserController::class, 'sync']);
        Route::get('{user}', [UserController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::patch('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy']);
    });

    // Product routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/', [ProductController::class, 'store']);
        Route::get('find-by-code', [ProductController::class, 'findByCode']);
        Route::get('find-by-barcode', [ProductController::class, 'findByBarcode']);
        Route::post('sync', [ProductController::class, 'sync']);
        Route::get('{product}', [ProductController::class, 'show']);
        Route::put('{product}', [ProductController::class, 'update']);
        Route::patch('{product}', [ProductController::class, 'update']);
        Route::delete('{product}', [ProductController::class, 'destroy']);
        Route::get('{product}/equivalents', [ProductController::class, 'equivalents']);
        Route::patch('{product}/stock', [ProductController::class, 'updateStock']);
        Route::patch('{product}/price', [ProductController::class, 'updatePrice']);
    });
});
