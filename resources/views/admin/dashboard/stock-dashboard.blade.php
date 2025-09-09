@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <div class="card-body text-white p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="display-6 mb-2">
                                <i class="fas fa-exclamation-triangle me-3"></i>Stock Alert Management
                            </h1>
                            <p class="lead mb-0">
                                Monitor inventory levels and manage alerts in real-time with automated reorder requests
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-light btn-lg me-2" onclick="refreshDashboard()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                            <button class="btn btn-warning btn-lg" onclick="triggerStockCheck()">
                                <i class="fas fa-search me-1"></i>Check Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4" id="stats-container">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading statistics...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label class="form-label">Status:</label>
                            <select class="form-select" id="statusFilter" onchange="applyFilters()">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="acknowledged">Acknowledged</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Severity:</label>
                            <select class="form-select" id="severityFilter" onchange="applyFilters()">
                                <option value="">All Severity</option>
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search Products:</label>
                            <input type="text" class="form-control" placeholder="Search products..." id="searchInput" onkeyup="applyFilters()">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="exportAlerts()">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Alerts Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Active Stock Alerts
                    </h5>
                </div>
                <div class="card-body" id="alerts-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading alerts from API...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reorder Requests Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Reorder Requests
                        </h5>
                        <button class="btn btn-light btn-sm" onclick="createReorderRequest()">
                            <i class="fas fa-plus me-1"></i>New Request
                        </button>
                    </div>
                </div>
                <div class="card-body" id="reorders-container">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading reorder requests from API...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Alerts -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="alertContainer"></div>
</div>

<script>
const API_BASE = '{{ url("/api/v1") }}';
let alertsData = [];
let reordersData = [];

// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Loading Stock Alert Dashboard...');
    loadDashboard();
});

async function loadDashboard() {
    console.log('📡 Fetching data from REST API endpoints...');
    await Promise.all([
        loadStats(),
        loadAlerts(), 
        loadReorders()
    ]);
}

async function loadStats() {
    try {
        console.log('📊 Fetching stats from:', `${API_BASE}/stock-alerts/stats`);
        const response = await fetch(`${API_BASE}/stock-alerts/stats`);
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            const statsHtml = `
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm border-start border-danger border-3">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                            <h3 class="text-danger">${data.critical_alerts}</h3>
                            <p class="mb-0 text-muted">Critical Alerts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm border-start border-warning border-3">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <h3 class="text-warning">${data.active_alerts}</h3>
                            <p class="mb-0 text-muted">Active Alerts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm border-start border-info border-3">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-cart fa-2x text-info mb-2"></i>
                            <h3 class="text-info">${data.pending_reorders}</h3>
                            <p class="mb-0 text-muted">Pending Reorders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm border-start border-success border-3">
                        <div class="card-body text-center">
                            <i class="fas fa-boxes fa-2x text-success mb-2"></i>
                            <h3 class="text-success">${data.low_stock_products}</h3>
                            <p class="mb-0 text-muted">Low Stock Items</p>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('stats-container').innerHTML = statsHtml;
        } else {
            throw new Error(result.message || 'Failed to load stats');
        }
    } catch (error) {
        console.error('❌ Error loading stats:', error);
        document.getElementById('stats-container').innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading statistics: ${error.message}
                </div>
            </div>
        `;
    }
}

async function loadAlerts() {
    try {
        console.log('🚨 Fetching alerts from:', `${API_BASE}/stock-alerts`);
        const response = await fetch(`${API_BASE}/stock-alerts`);
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            alertsData = result.data;
            renderAlerts(alertsData);
        } else {
            document.getElementById('alerts-container').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>No Active Alerts</h5>
                    <p class="text-muted">All stock levels are currently within normal ranges.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('❌ Error loading alerts:', error);
        document.getElementById('alerts-container').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading alerts: ${error.message}
            </div>
        `;
    }
}

function renderAlerts(alerts) {
    const alertsHtml = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Alert Type</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${alerts.map(alert => `
                        <tr>
                            <td>
                                <strong>${alert.product_name || 'Product #' + alert.product_id}</strong>
                                <br><small class="text-muted">#${alert.product_id}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    ${alert.alert_type.replace('_', ' ').toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <strong class="${alert.current_quantity === 0 ? 'text-danger' : ''}">
                                    ${alert.current_quantity}
                                </strong>
                            </td>
                            <td>${alert.reorder_level}</td>
                            <td>
                                <span class="badge bg-${getSeverityColor(alert.severity)}">
                                    ${alert.severity.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-${getStatusColor(alert.status)}">
                                    ${alert.status.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <small>${new Date(alert.created_at).toLocaleDateString()}<br>
                                ${new Date(alert.created_at).toLocaleTimeString()}</small>
                            </td>
                            <td>
                                ${getActionButton(alert)}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    document.getElementById('alerts-container').innerHTML = alertsHtml;
}

async function loadReorders() {
    try {
        console.log('📋 Fetching reorders from:', `${API_BASE}/reorder-requests`);
        const response = await fetch(`${API_BASE}/reorder-requests`);
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            reordersData = result.data;
            renderReorders(reordersData);
        } else {
            document.getElementById('reorders-container').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-info mb-3"></i>
                    <h5>No Reorder Requests</h5>
                    <p class="text-muted">No pending reorder requests at this time.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('❌ Error loading reorders:', error);
        document.getElementById('reorders-container').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading reorder requests: ${error.message}
            </div>
        `;
    }
}

function renderReorders(reorders) {
    const reordersHtml = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Suggested Qty</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Supplier</th>
                        <th>Est. Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${reorders.map(reorder => `
                        <tr>
                            <td>
                                <strong>${reorder.product_name || 'Product #' + reorder.product_id}</strong>
                                <br><small class="text-muted">#${reorder.product_id}</small>
                            </td>
                            <td>
                                <strong class="${reorder.current_quantity === 0 ? 'text-danger' : ''}">
                                    ${reorder.current_quantity}
                                </strong>
                            </td>
                            <td><strong>${reorder.suggested_quantity}</strong></td>
                            <td>
                                <span class="badge bg-${getPriorityColor(reorder.priority)}">
                                    ${reorder.priority.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-${getStatusColor(reorder.status)}">
                                    ${reorder.status.toUpperCase()}
                                </span>
                            </td>
                            <td>${reorder.supplier || 'N/A'}</td>
                            <td>$${reorder.estimated_cost || 'N/A'}</td>
                            <td>
                                ${reorder.status === 'pending' ? 
                                    `<button class="btn btn-success btn-sm" onclick="approveReorder(${reorder.id})">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>` : 
                                    `<span class="badge bg-${getStatusColor(reorder.status)}">
                                        ${reorder.status.toUpperCase()}
                                    </span>`
                                }
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    document.getElementById('reorders-container').innerHTML = reordersHtml;
}

// Helper functions for styling
function getSeverityColor(severity) {
    switch(severity) {
        case 'critical': return 'danger';
        case 'high': return 'warning';
        case 'medium': return 'info';
        case 'low': return 'secondary';
        default: return 'secondary';
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'active': return 'danger';
        case 'acknowledged': return 'warning';
        case 'resolved': return 'success';
        case 'pending': return 'info';
        case 'approved': return 'success';
        default: return 'secondary';
    }
}

function getPriorityColor(priority) {
    switch(priority) {
        case 'urgent': return 'danger';
        case 'high': return 'warning';
        case 'medium': return 'info';
        case 'low': return 'secondary';
        default: return 'secondary';
    }
}

function getActionButton(alert) {
    if (alert.status === 'active') {
        return `<button class="btn btn-warning btn-sm" onclick="acknowledgeAlert(${alert.id})">
                    <i class="fas fa-check me-1"></i>Acknowledge
                </button>`;
    } else if (alert.status === 'acknowledged') {
        return `<button class="btn btn-success btn-sm" onclick="resolveAlert(${alert.id})">
                    <i class="fas fa-check-double me-1"></i>Resolve
                </button>`;
    } else {
        return `<span class="badge bg-success">RESOLVED</span>`;
    }
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const severityFilter = document.getElementById('severityFilter').value;
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();

    let filteredAlerts = alertsData.filter(alert => {
        const matchesStatus = !statusFilter || alert.status === statusFilter;
        const matchesSeverity = !severityFilter || alert.severity === severityFilter;
        const matchesSearch = !searchTerm || 
            (alert.product_name && alert.product_name.toLowerCase().includes(searchTerm)) ||
            alert.product_id.toString().includes(searchTerm);
        
        return matchesStatus && matchesSeverity && matchesSearch;
    });

    renderAlerts(filteredAlerts);
}

async function refreshDashboard() {
    showAlert('info', 'Refreshing', 'Updating dashboard data...');
    await loadDashboard();
    showAlert('success', 'Refreshed', 'Dashboard data updated successfully');
}

async function triggerStockCheck() {
    try {
        console.log('⚡ Triggering stock check via API...');
        showAlert('info', 'Stock Check', 'Running stock level check...');
        
        const response = await fetch(`${API_BASE}/stock-alerts/trigger-check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', 'Stock Check Complete', result.message);
            await refreshDashboard();
        } else {
            throw new Error(result.message || 'Stock check failed');
        }
    } catch (error) {
        console.error('❌ Error triggering stock check:', error);
        showAlert('danger', 'Error', 'Stock check failed: ' + error.message);
    }
}

async function acknowledgeAlert(alertId) {
    try {
        const response = await fetch(`${API_BASE}/stock-alerts/${alertId}/acknowledge`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', 'Alert Acknowledged', 'Alert has been acknowledged successfully');
            await loadAlerts();
        } else {
            throw new Error(result.message || 'Failed to acknowledge alert');
        }
    } catch (error) {
        console.error('❌ Error acknowledging alert:', error);
        showAlert('danger', 'Error', 'Failed to acknowledge alert: ' + error.message);
    }
}

async function resolveAlert(alertId) {
    try {
        const response = await fetch(`${API_BASE}/stock-alerts/${alertId}/resolve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', 'Alert Resolved', 'Alert has been resolved successfully');
            await loadAlerts();
        } else {
            throw new Error(result.message || 'Failed to resolve alert');
        }
    } catch (error) {
        console.error('❌ Error resolving alert:', error);
        showAlert('danger', 'Error', 'Failed to resolve alert: ' + error.message);
    }
}

async function approveReorder(reorderId) {
    try {
        const response = await fetch(`${API_BASE}/reorder-requests/${reorderId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('success', 'Reorder Approved', 'Reorder request has been approved successfully');
            await loadReorders();
        } else {
            throw new Error(result.message || 'Failed to approve reorder');
        }
    } catch (error) {
        console.error('❌ Error approving reorder:', error);
        showAlert('danger', 'Error', 'Failed to approve reorder: ' + error.message);
    }
}

function showAlert(type, title, message) {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = Date.now();
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" id="alert-${alertId}" role="alert">
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alertElement = document.getElementById(`alert-${alertId}`);
        if (alertElement) {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }
    }, 5000);
}

function exportAlerts() {
    showAlert('info', 'Export', 'Export functionality will be implemented soon');
}

function createReorderRequest() {
    showAlert('info', 'Create Request', 'Manual reorder request creation will be implemented soon');
}
</script>
@endsection
