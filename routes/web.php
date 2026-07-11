<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VariantTypeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FragranceController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('variant-types', VariantTypeController::class);
Route::resource('product-variants', ProductVariantController::class);
Route::resource('fragrances', FragranceController::class);
Route::get('sales-orders', [SalesOrderController::class, 'index'])->name('sales-orders.index');
Route::get('sales-orders/create', [SalesOrderController::class, 'create'])->name('sales-orders.create');
Route::get('sales-orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales-orders.show');
Route::post('sales-orders', [SalesOrderController::class, 'store'])->name('sales-orders.store');
Route::delete('sales-orders/{salesOrder}', [SalesOrderController::class, 'destroy'])
    ->name('sales-orders.destroy');
Route::get('/dashboard/profit', [ReportController::class, 'profitDashboard'])
    ->name('dashboard.profit');
Route::get('reports/best-seller-fragrances', [ReportController::class, 'bestSellerFragrances'])
    ->name('reports.best-seller-fragrances');
Route::get('reports/total-profit', [ReportController::class, 'totalProfit'])
    ->name('reports.total-profit');

Route::get('/debug-log-write-test', function () {
    $message = 'DEBUG LOG TEST ' . now();
    $directFile = storage_path('logs/direct-test.log');
    $laravelLog = storage_path('logs/laravel.log');

    logger()->error($message);
    file_put_contents($directFile, $message . PHP_EOL, FILE_APPEND);

    return response()->json([
        'ok' => true,
        'storage_path' => storage_path(),
        'laravel_log' => $laravelLog,
        'direct_file' => $directFile,
        'logs_writable' => is_writable(storage_path('logs')),
        'laravel_log_exists' => file_exists($laravelLog),
        'laravel_log_writable' => file_exists($laravelLog) ? is_writable($laravelLog) : null,
        'direct_file_exists' => file_exists($directFile),
        'direct_file_writable' => file_exists($directFile) ? is_writable($directFile) : null,
    ]);
});
