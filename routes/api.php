<?php

use App\Http\Controllers\Api\FragranceController;
use App\Http\Controllers\Api\ProductVariantApiController;
use App\Http\Controllers\Api\SalesOrderApiController;
use App\Http\Controllers\Api\VariantTypeApiController;
use Illuminate\Support\Facades\Route;

// Keep API route names separate from the web resource route names. Without
// this prefix, helpers such as route('sales-orders.show') resolve to the API
// endpoint and open a JSON response from the web interface.
Route::name('api.')->group(function (): void {
    Route::apiResource('fragrances', FragranceController::class);
    Route::apiResource('variant-types', VariantTypeApiController::class);
    Route::apiResource('product-variants', ProductVariantApiController::class);
    Route::apiResource('sales-orders', SalesOrderApiController::class);
});
