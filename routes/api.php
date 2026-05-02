<?php

use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CustomerBasketController;
use App\Http\Controllers\Api\CustomerOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer')->group(function (): void {
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/baskets', [CustomerBasketController::class, 'index']);
    Route::get('/baskets/{basketSlug}', [CustomerBasketController::class, 'show']);

    Route::middleware(['auth:sanctum', 'customer.api'])->group(function (): void {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/me', [CustomerAuthController::class, 'me']);
        Route::post('/orders/review', [CustomerOrderController::class, 'review']);
        Route::get('/orders', [CustomerOrderController::class, 'index']);
        Route::post('/orders', [CustomerOrderController::class, 'store']);
        Route::get('/orders/{orderId}', [CustomerOrderController::class, 'show']);
    });
});
