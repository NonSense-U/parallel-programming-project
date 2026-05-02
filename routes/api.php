<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/product-sales-reports', [AdminController::class, 'generateProductSalesReports']);
});

Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [CartController::class, 'checkout']);
    Route::post('/items', [CartController::class, 'addItem']);
    Route::delete('/items/{productId}', [CartController::class, 'removeItem']);
    Route::delete('/empty', [CartController::class, 'empty']);
    Route::get('/show', [CartController::class, 'show']);
});

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
