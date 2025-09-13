<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PatientController;

Route::get('/', function () {
    return view('home');
})->name('home');

// Public products page
Route::get('/products', function () {
    $products = \App\Models\Product::where('is_active', 1)
                                   ->orderBy('category')
                                   ->orderBy('name')
                                   ->get();
    return view('products', compact('products'));
})->name('products');

// Fallback login route for auth middleware redirects
Route::get('/login', function () {
    return redirect()->route('home');
})->name('login');

// Original dashboard (now staff-only)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Patient Management Routes
Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
Route::post('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
Route::post('/patients/{patient}/delete', [PatientController::class, 'destroy'])->name('patients.destroy');

// Clinic workflow routes removed - functionality moved to patient portal

// Authentication Routes
// Patient Authentication
Route::get('/patient/register', [App\Http\Controllers\PatientAuthController::class, 'showRegisterForm'])->name('patient.register');
Route::post('/patient/register', [App\Http\Controllers\PatientAuthController::class, 'register']);
Route::post('/patient/login', [App\Http\Controllers\PatientAuthController::class, 'login'])->name('patient.login');
Route::get('/patient/dashboard', [App\Http\Controllers\PatientAuthController::class, 'dashboard'])->name('patient.dashboard');
Route::post('/patient/logout', [App\Http\Controllers\PatientAuthController::class, 'logout'])->name('patient.logout');

// Staff Authentication
Route::post('/staff/login', [App\Http\Controllers\StaffAuthController::class, 'login'])->name('staff.login');
Route::get('/staff/dashboard', [App\Http\Controllers\StaffAuthController::class, 'dashboard'])->name('staff.dashboard');
Route::post('/staff/logout', [App\Http\Controllers\StaffAuthController::class, 'logout'])->name('staff.logout');
Route::get('/create-admin', [App\Http\Controllers\StaffAuthController::class, 'createDefaultAdmin'])->name('create.admin');

// Staff module (from clinic-system1) routes - isolated namespace, keep teammate pattern
Route::prefix('staffmod')->name('staffmod.')->group(function(){
    // Auth
    Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('/admin/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/staff-list', [\App\Http\Controllers\AdminController::class, 'listStaff'])->name('admin.staffList');
    Route::get('/admin/createStaff', [\App\Http\Controllers\AdminController::class, 'showCreateStaffForm'])->name('admin.createStaff');
    Route::post('/admin/createStaff', [\App\Http\Controllers\AdminController::class, 'createStaff'])->name('admin.createStaff.do');
    Route::post('/admin/receptionist/{staffId}/activate', [\App\Http\Controllers\AdminController::class, 'activateReceptionist'])->name('admin.receptionist.activate');
    Route::post('/admin/receptionist/{staffId}/deactivate', [\App\Http\Controllers\AdminController::class, 'deactivateReceptionist'])->name('admin.receptionist.deactivate');
    

    
    // Stock Alert routes for staff admin
    Route::get('/admin/stock-alerts', [App\Http\Controllers\Admin\StockAlertController::class, 'index'])->name('admin.stock.alerts');
    Route::post('/admin/stock-alerts/{id}/acknowledge', [App\Http\Controllers\Admin\StockAlertController::class, 'acknowledge'])->name('admin.stock.acknowledge');
    Route::post('/admin/stock-alerts/{id}/resolve', [App\Http\Controllers\Admin\StockAlertController::class, 'resolve'])->name('admin.stock.resolve');
    Route::post('/admin/stock-alerts/{id}/create-reorder', [App\Http\Controllers\Admin\StockAlertController::class, 'createReorderRequest'])->name('admin.stock.create-reorder');
    Route::post('/admin/stock-alerts/trigger-check', [App\Http\Controllers\Admin\StockAlertController::class, 'triggerStockCheck'])->name('admin.stock.trigger-check');
    Route::post('/admin/reorder-requests/{id}/approve', [App\Http\Controllers\Admin\StockAlertController::class, 'approveReorder'])->name('admin.reorder.approve');
    Route::post('/admin/reorder-requests/{id}/cancel', [App\Http\Controllers\Admin\StockAlertController::class, 'cancelReorder'])->name('admin.reorder.cancel');

    // Product Management routes for staff admin
    Route::get('/admin/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products');
    Route::get('/admin/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{id}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.delete');

    // Doctor minimal routes
    Route::get('/doctor/dashboard', [\App\Http\Controllers\DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::get('/doctor/schedule', [\App\Http\Controllers\DoctorController::class, 'manageSchedule'])->name('doctor.schedule');
    Route::post('/doctor/schedule', [\App\Http\Controllers\DoctorController::class, 'store'])->name('doctor.storeSchedule');
    Route::delete('/doctor/schedule/{id}', [\App\Http\Controllers\DoctorController::class, 'deleteSchedule'])->name('doctor.deleteSchedule');

    // Receptionist minimal routes
    Route::get('/receptionist/dashboard', [\App\Http\Controllers\ReceptionistController::class, 'dashboard'])->name('receptionist.dashboard');
    Route::get('/receptionist/appointments', [\App\Http\Controllers\ReceptionistController::class, 'appointmentManagement'])->name('receptionist.appointments');
    Route::get('/receptionist/inbox', [\App\Http\Controllers\Receptionist\InboxController::class, 'index'])->name('receptionist.inbox');
    Route::get('/receptionist/message/{message}', [\App\Http\Controllers\Receptionist\InboxController::class, 'show'])->name('receptionist.message.show');
    Route::post('/receptionist/message/{message}/reply', [\App\Http\Controllers\Receptionist\InboxController::class, 'reply'])->name('receptionist.message.reply');
});

// Patient messaging routes
Route::prefix('patient')->name('patient.')->middleware('auth')->group(function(){
    Route::get('/messages', [\App\Http\Controllers\Patient\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/new', [\App\Http\Controllers\Patient\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [\App\Http\Controllers\Patient\MessageController::class, 'store'])->name('messages.store');
});

// Patient portal: profile only (appointments handled by teammate)
Route::prefix('patient')->name('patient.')->middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Patient\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\Patient\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\Patient\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/deactivate', [App\Http\Controllers\Patient\ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::get('/profile/create', [App\Http\Controllers\Patient\ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile/store', [App\Http\Controllers\Patient\ProfileController::class, 'store'])->name('profile.store');
    Route::post('/profile/delete', [App\Http\Controllers\Patient\ProfileController::class, 'destroy'])->name('profile.destroy');

    // Appointments (patient self-service)
    Route::get('/appointments', [App\Http\Controllers\Patient\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [App\Http\Controllers\Patient\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\Patient\AppointmentController::class, 'store'])->name('appointments.store');
});

// Admin routes (consolidated) - using proper Admin namespace
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard routes
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Stock Alerts (via Admin namespace) 
    Route::get('/stock-alerts', [App\Http\Controllers\Admin\StockAlertController::class, 'index'])->name('stock.alerts');
    Route::post('/stock-alerts/{id}/acknowledge', [App\Http\Controllers\Admin\StockAlertController::class, 'acknowledge'])->name('stock.acknowledge');
    Route::post('/stock-alerts/{id}/resolve', [App\Http\Controllers\Admin\StockAlertController::class, 'resolve'])->name('stock.resolve');
});

// Payment routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentController::class, 'failure'])->name('failure');
});
