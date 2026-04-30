<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::delete('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [\App\Http\Controllers\CartController::class, 'checkout']);
    Route::post('/items', [\App\Http\Controllers\CartController::class, 'addItem']);
    Route::delete('/items/{productId}', [\App\Http\Controllers\CartController::class, 'removeItem']);
    Route::delete('/empty', [\App\Http\Controllers\CartController::class, 'empty']);
});

Route::prefix('orders')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [\App\Http\Controllers\OrderController::class, 'index']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});