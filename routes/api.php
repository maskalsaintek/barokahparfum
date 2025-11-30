<?php

use App\Http\Controllers\Api\VariantTypeApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FragranceController;
use App\Http\Controllers\ProductVariantController;

Route::apiResource('fragrances', FragranceController::class);
Route::apiResource('variant-types', VariantTypeApiController::class);

Route::resource('product-variants', ProductVariantController::class)->except(['show']);
