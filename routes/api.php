<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('merchant.auth')->post('/orders', [OrderController::class, 'store']);
