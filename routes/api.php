<?php

use App\Http\Controllers\Api\FragranceController;
use App\Http\Controllers\Api\ProductVariantApiController;
use App\Http\Controllers\Api\SalesOrderApiController;
use App\Http\Controllers\Api\VariantTypeApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('fragrances', FragranceController::class);
Route::apiResource('variant-types', VariantTypeApiController::class);

Route::apiResource('product-variants', ProductVariantApiController::class);
Route::apiResource('sales-orders', SalesOrderApiController::class);
