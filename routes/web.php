<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('welcome');
});

// Public routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard routes (updated for folder structure)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/stock-dashboard', [DashboardController::class, 'stockDashboard'])->name('stock.dashboard');
    
    // Additional admin routes
    Route::get('/stock-alerts', [DashboardController::class, 'stockDashboard'])->name('stock.alerts');
    
    // Admin Product Management Routes
    Route::resource('products', AdminProductController::class)->except(['show']);
});

// Legacy route redirect (for backwards compatibility)
Route::get('/stock-dashboard', function () {
    return redirect()->route('admin.stock.dashboard');
});

// Payment routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentController::class, 'failure'])->name('failure');
});
