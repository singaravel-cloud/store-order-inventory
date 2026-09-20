<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);

Route::get('/customers/orders',[OrderController::class, 'customerHistory']);

Route::get('/products/low-stock',[OrderController::class, 'lowStock']);