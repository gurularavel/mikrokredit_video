<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('merchant.auth')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/status', [OrderController::class, 'status']);
});
