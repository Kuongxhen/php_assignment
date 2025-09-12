<?php $__env->startSection('content'); ?>
<div class="container">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-6 mb-2">
                                <i class="fas fa-tachometer-alt me-3"></i>Admin Dashboard
                            </h1>
                            <p class="lead mb-0">
                                Comprehensive inventory management system with real-time stock monitoring and RESTful API integration
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex flex-column align-items-end">
                                <div class="mb-2">
                                    <span class="badge bg-light text-dark px-3 py-2">
                                        <i class="fas fa-server me-1"></i>Laravel <?php echo e(app()->version()); ?>

                                    </span>
                                </div>
                                <div>
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>System Online
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.stock.dashboard')); ?>" class="btn btn-primary w-100 py-3 h-100">
                                <i class="fas fa-exclamation-triangle d-block fs-3 mb-2"></i>
                                <strong>Stock Alerts</strong><br>
                                <small>Monitor inventory levels</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-info w-100 py-3 h-100">
                                <i class="fas fa-boxes d-block fs-3 mb-2"></i>
                                <strong>Manage Products</strong><br>
                                <small>Add & edit products</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(url('/api/v1/stock-alerts/stats')); ?>" target="_blank" class="btn btn-success w-100 py-3 h-100">
                                <i class="fas fa-chart-line d-block fs-3 mb-2"></i>
                                <strong>API Stats</strong><br>
                                <small>Real-time statistics</small>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-secondary w-100 py-3 h-100">
                                <i class="fas fa-boxes d-block fs-3 mb-2"></i>
                                <strong>Products</strong><br>
                                <small>View product catalog</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Overview -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>System Features
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-eye text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Observer Pattern</h6>
                                    <p class="text-muted small mb-0">Automatic stock alert notifications using GoF Observer design pattern</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-code text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">RESTful API</h6>
                                    <p class="text-muted small mb-0">Complete REST API with proper HTTP methods and JSON responses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-bell text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Real-time Alerts</h6>
                                    <p class="text-muted small mb-0">Instant notifications for low stock and reorder requirements</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-database text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Data Management</h6>
                                    <p class="text-muted small mb-0">Comprehensive product, payment, and inventory tracking</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link text-success me-2"></i>API Endpoints
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo e(url('/api/v1/products')); ?>" target="_blank" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success me-2">GET</span>
                                    <small>Products</small>
                                </div>
                                <i class="fas fa-external-link-alt text-muted"></i>
                            </div>
                        </a>
                        <a href="<?php echo e(url('/api/v1/stock-alerts')); ?>" target="_blank" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success me-2">GET</span>
                                    <small>Stock Alerts</small>
                                </div>
                                <i class="fas fa-external-link-alt text-muted"></i>
                            </div>
                        </a>
                        <a href="<?php echo e(url('/api/v1/reorder-requests')); ?>" target="_blank" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success me-2">GET</span>
                                    <small>Reorder Requests</small>
                                </div>
                                <i class="fas fa-external-link-alt text-muted"></i>
                            </div>
                        </a>
                        <a href="<?php echo e(url('/api/v1/payments')); ?>" target="_blank" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-success me-2">GET</span>
                                    <small>Payments</small>
                                </div>
                                <i class="fas fa-external-link-alt text-muted"></i>
                            </div>
                        </a>
                        <a href="<?php echo e(url('/api/v1/health')); ?>" target="_blank" class="list-group-item list-group-item-action border-0 px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-info me-2">GET</span>
                                    <small>Health Check</small>
                                </div>
                                <i class="fas fa-external-link-alt text-muted"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Grid -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock text-warning me-2"></i>Quick Navigation
                            </h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 col-6 mb-3">
                            <a href="<?php echo e(route('admin.stock.dashboard')); ?>" class="text-decoration-none">
                                <div class="border rounded p-3 h-100 hover-shadow">
                                    <i class="fas fa-exclamation-triangle text-danger fs-1 mb-2"></i>
                                    <h6 class="mb-1">Stock Alerts</h6>
                                    <small class="text-muted">Monitor critical levels</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <a href="<?php echo e(route('products.index')); ?>" class="text-decoration-none">
                                <div class="border rounded p-3 h-100 hover-shadow">
                                    <i class="fas fa-boxes text-primary fs-1 mb-2"></i>
                                    <h6 class="mb-1">Products</h6>
                                    <small class="text-muted">Browse catalog</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <a href="<?php echo e(route('payment.index')); ?>" class="text-decoration-none">
                                <div class="border rounded p-3 h-100 hover-shadow">
                                    <i class="fas fa-credit-card text-success fs-1 mb-2"></i>
                                    <h6 class="mb-1">Payments</h6>
                                    <small class="text-muted">Process transactions</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <a href="<?php echo e(url('/api/v1/health')); ?>" target="_blank" class="text-decoration-none">
                                <div class="border rounded p-3 h-100 hover-shadow">
                                    <i class="fas fa-heartbeat text-info fs-1 mb-2"></i>
                                    <h6 class="mb-1">API Health</h6>
                                    <small class="text-muted">System status</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <a href="<?php echo e(url('/api/v1/stock-alerts/stats')); ?>" target="_blank" class="text-decoration-none">
                                <div class="border rounded p-3 h-100 hover-shadow">
                                    <i class="fas fa-chart-bar text-warning fs-1 mb-2"></i>
                                    <h6 class="mb-1">Statistics</h6>
                                    <small class="text-muted">Real-time data</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <a href="<?php echo e(route('admin.products.index')); ?>" class="text-decoration-none">
                                <div class="border rounded p-3 h-100 hover-shadow">
                                    <i class="fas fa-cog text-secondary fs-1 mb-2"></i>
                                    <h6 class="mb-1">Admin Products</h6>
                                    <small class="text-muted">Manage inventory</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/admin/dashboard/dashboard.blade.php ENDPATH**/ ?>