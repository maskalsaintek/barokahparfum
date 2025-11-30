<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VariantTypeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\SalesOrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('variant-types', VariantTypeController::class);
Route::resource('product-variants', ProductVariantController::class);
Route::get('sales-orders', [SalesOrderController::class, 'index'])->name('sales-orders.index');
Route::get('sales-orders/create', [SalesOrderController::class, 'create'])->name('sales-orders.create');
Route::get('sales-orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales-orders.show');
Route::post('sales-orders', [SalesOrderController::class, 'store'])->name('sales-orders.store');
Route::delete('sales-orders/{salesOrder}', [SalesOrderController::class, 'destroy'])
    ->name('sales-orders.destroy');
