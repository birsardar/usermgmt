<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrderController;

Route::post('/auth/register', [UserController::class, 'createUser'])->name('auth.register');
Route::post('/auth/login', [UserController::class, 'loginUser'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::put('user/profile', [UserController::class, 'editProfile']);
    Route::get('user/orders', [UserController::class, 'orderHistory']);

    // Order routes
    Route::post('orders', [OrderController::class, 'placeOrder']);
    Route::get('orders/{id}', [OrderController::class, 'trackOrder']);
    Route::put('orders/{id}/payment-status', [OrderController::class, 'updatePaymentStatus']);
});
