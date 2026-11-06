<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [InventoryController::class, 'products']);
Route::get('/products/{id}', [InventoryController::class, 'product'])->whereNumber('id');
Route::post('/products', [InventoryController::class, 'createProduct']);
Route::post('/stock-in', [InventoryController::class, 'stockIn']);
Route::post('/stock-out', [InventoryController::class, 'stockOut']);
Route::get('/products/{id}/stock-value', [InventoryController::class, 'stockValue'])->whereNumber('id');
Route::get('/products/{id}/stock-status', [InventoryController::class, 'stockStatus'])->whereNumber('id');
Route::get('/categories', [InventoryController::class, 'categories']);
Route::get('/suppliers', [InventoryController::class, 'suppliers']);
Route::get('/transactions', [InventoryController::class, 'transactions']);
