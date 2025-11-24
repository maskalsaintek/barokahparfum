<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FragranceController;

Route::apiResource('fragrances', FragranceController::class);
