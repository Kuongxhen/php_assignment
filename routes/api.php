<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StockAlertController;
use App\Http\Controllers\Api\ReorderRequestController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\V1\PatientController;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// From Michael and Marcus
// Public authentication routes (no auth required)
Route::prefix('v1/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected API v1 routes group (require authentication)
Route::prefix('v1')->middleware(['auth'])->group(function () {
    
    // Authentication routes (require auth)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::delete('/account', [AuthController::class, 'deleteAccount']);
        Route::get('/permissions', [AuthController::class, 'permissions']);
    });
    
    // Patient API routes
    Route::apiResource('patients', PatientController::class);
    Route::get('/patients/search', [PatientController::class, 'search']);
    Route::get('/patients/statistics', [PatientController::class, 'statistics']);
    Route::patch('/patients/{patient}/status', [PatientController::class, 'updateStatus']);
    Route::get('/patients/deletion-candidates', [PatientController::class, 'eligibleForDeletion']);
    
    // Patient Care API routes - Decorator Pattern Implementation
    Route::prefix('patient-care')->group(function () {
        // Care plan endpoints removed with decorator demo
    });
});

// Role-based routes with middleware
Route::prefix('v1')->middleware(['auth', 'role:admin'])->group(function () {
    // Admin-only routes
    Route::delete('/patients/{patient}/force-delete', [PatientController::class, 'forceDelete']);
    Route::get('/system/statistics', function () {
        return response()->json([
            'status' => 'success',
            'data' => [
                'users' => \App\Models\User::getStatistics(),
                'patients' => \App\Models\Patient::getStatistics(),
            ]
        ]);
    });
});

Route::prefix('v1')->middleware(['auth', 'role:doctor,admin'])->group(function () {
    // Doctor and Admin only routes
    Route::get('/reports/patients', [PatientController::class, 'reports']);
});

Route::prefix('v1')->middleware(['auth', 'role:staff,doctor,admin'])->group(function () {
    // Staff, Doctor, and Admin routes
    Route::get('/patients/all', [PatientController::class, 'index']);
});

// API version 1 KuongXhen part
Route::prefix('v1')->group(function () {
    
    // Product API routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('api.products.index');
        Route::get('/{id}', [ProductController::class, 'show'])->name('api.products.show');
        Route::get('/category/{category}', [ProductController::class, 'getByCategory'])->name('api.products.category');
        Route::post('/check-availability', [ProductController::class, 'checkAvailability'])->name('api.products.check-availability');
        Route::post('/', [ProductController::class, 'store'])->name('api.products.store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('api.products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('api.products.destroy');
    });

    // Payment API routes
    Route::prefix('payments')->group(function () {
        Route::post('/process', [PaymentController::class, 'processPayment'])->name('api.payments.process');
        Route::get('/methods', [PaymentController::class, 'getPaymentMethods'])->name('api.payments.methods');
        Route::get('/{paymentId}/status', [PaymentController::class, 'getPaymentStatus'])->name('api.payments.status');
        Route::get('/appointment/{appointmentId}', [PaymentController::class, 'getPaymentsByAppointment'])->name('api.payments.by-appointment');
        Route::post('/{paymentId}/refund', [PaymentController::class, 'refundPayment'])->name('api.payments.refund');
        Route::get('/', [PaymentController::class, 'index'])->name('api.payments.index');
        Route::get('/{id}', [PaymentController::class, 'show'])->name('api.payments.show');
    });

    // User API routes (for user management)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('api.users.index');
        Route::get('/{id}', [UserController::class, 'show'])->name('api.users.show');
        Route::post('/', [UserController::class, 'store'])->name('api.users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('api.users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('api.users.destroy');
    });

    // Stock Alert API routes (Admin dashboard)
    Route::prefix('stock-alerts')->group(function () {
        Route::get('/', [StockAlertController::class, 'index'])->name('api.stock-alerts.index');
        Route::get('/stats', [StockAlertController::class, 'stats'])->name('api.stock-alerts.stats');
        Route::post('/', [StockAlertController::class, 'store'])->name('api.stock-alerts.store');
        Route::get('/{id}', [StockAlertController::class, 'show'])->name('api.stock-alerts.show');
        Route::put('/{id}', [StockAlertController::class, 'update'])->name('api.stock-alerts.update');
        Route::post('/{id}/acknowledge', [StockAlertController::class, 'acknowledge'])->name('api.stock-alerts.acknowledge');
        Route::post('/{id}/resolve', [StockAlertController::class, 'resolve'])->name('api.stock-alerts.resolve');
        Route::post('/trigger-check', [StockAlertController::class, 'triggerStockCheck'])->name('api.stock-alerts.trigger-check');
        Route::delete('/{id}', [StockAlertController::class, 'destroy'])->name('api.stock-alerts.destroy');
    });

    // Reorder Request API routes
    Route::prefix('reorder-requests')->group(function () {
        Route::get('/', [ReorderRequestController::class, 'index'])->name('api.reorder-requests.index');
        Route::get('/stats', [ReorderRequestController::class, 'stats'])->name('api.reorder-requests.stats');
        Route::post('/', [ReorderRequestController::class, 'store'])->name('api.reorder-requests.store');
        Route::get('/{id}', [ReorderRequestController::class, 'show'])->name('api.reorder-requests.show');
        Route::put('/{id}', [ReorderRequestController::class, 'update'])->name('api.reorder-requests.update');
        Route::post('/{id}/approve', [ReorderRequestController::class, 'approve'])->name('api.reorder-requests.approve');
        Route::post('/{id}/cancel', [ReorderRequestController::class, 'cancel'])->name('api.reorder-requests.cancel');
        Route::post('/{id}/mark-ordered', [ReorderRequestController::class, 'markOrdered'])->name('api.reorder-requests.mark-ordered');
        Route::post('/{id}/mark-received', [ReorderRequestController::class, 'receive'])->name('api.reorder-requests.mark-received');
        Route::delete('/{id}', [ReorderRequestController::class, 'destroy'])->name('api.reorder-requests.destroy');
    });

    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    })->name('api.health');

});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'error' => 'The requested API endpoint does not exist.'
    ], 404);
});