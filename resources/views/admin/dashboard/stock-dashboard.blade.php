@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Alert Management</h1>
                <p class="page-subtitle">Monitor and manage low stock alerts and reorder suggestions</p>
            </div>
            <div class="alert-badges">
                <div class="alert-badge active">
                    <i class="fas fa-bell"></i>
                    <span id="alert-count">0</span>
                    Active Alerts
                </div>
                <div class="alert-badge critical">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="critical-badge-count">0</span>
                    Critical
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-row">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" placeholder="Search alerts..." id="searchInput" onkeyup="debouncedSearch()">
            </div>
            <select class="filter-select" id="priorityFilter" onchange="applyFilters()">
                <option value="">All Priority</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <select class="filter-select" id="statusFilter" onchange="applyFilters()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="acknowledged">Acknowledged</option>
                <option value="resolved">Resolved</option>
            </select>
            <button class="action-btn" onclick="triggerStockCheck()" style="background: var(--primary-blue); color: white; border: none;">
                <i class="fas fa-sync"></i>
                Check Stock Levels
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="content-grid">
        <!-- Stock Alerts Section -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-exclamation-triangle section-icon"></i>
                    Stock Alerts
                </h2>
            </div>
            <div class="section-body" id="alerts-container">
                <div class="loading-state">
                    <div class="spinner"></div>
                    <p>Loading stock alerts...</p>
                </div>
            </div>
        </div>

        <!-- Reorder Suggestions Section -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-shopping-cart section-icon blue"></i>
                    Reorder Suggestions
                </h2>
            </div>
            <div class="section-body" id="reorders-container">
                <div class="loading-state">
                    <div class="spinner"></div>
                    <p>Loading reorder suggestions...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Summary Section -->
    <div class="summary-section">
        <h2 class="summary-title">Alert Summary</h2>
        <div class="summary-grid" id="alert-summary-container">
            <div class="summary-item">
                <div class="summary-dot critical"></div>
                <span class="summary-label">Critical</span>
                <span class="summary-count" id="critical-count">1</span>
            </div>
            <div class="summary-item">
                <div class="summary-dot high"></div>
                <span class="summary-label">High Priority</span>
                <span class="summary-count" id="high-count">1</span>
            </div>
            <div class="summary-item">
                <div class="summary-dot medium"></div>
                <span class="summary-label">Medium Priority</span>
                <span class="summary-count" id="medium-count">1</span>
            </div>
            <div class="summary-item">
                <div class="summary-dot resolved"></div>
                <span class="summary-label">Resolved Today</span>
                <span class="summary-count" id="resolved-count">1</span>
            </div>
        </div>
    </div>
</div>

<!-- Alert Container for Notifications -->
<div id="alertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

<!-- Medical Theme Styles -->
<style>
:root {
    --primary-blue: #3b82f6;
    --secondary-gray: #f8fafc;
    --border-gray: #e2e8f0;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --orange-bg: #fed7aa;
    --orange-text: #ea580c;
    --red-bg: #fecaca;
    --red-text: #dc2626;
    --blue-bg: #dbeafe;
    --blue-text: #2563eb;
    --green-bg: #dcfce7;
    --green-text: #16a34a;
    --yellow-bg: #fef3c7;
    --yellow-text: #d97706;
}

body {
    background: #f1f5f9;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--text-dark);
}

.container-fluid {
    max-width: 1400px;
}

/* Header Section */
.page-header {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-gray);
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: var(--text-muted);
    font-size: 0.95rem;
    margin: 0;
}

.alert-badges {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.alert-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
}

.alert-badge.active {
    background: var(--orange-bg);
    color: var(--orange-text);
}

.alert-badge.critical {
    background: var(--red-bg);
    color: var(--red-text);
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-gray);
}

.filter-row {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-gray);
    border-radius: 8px;
    font-size: 0.875rem;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-select {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-gray);
    border-radius: 8px;
    font-size: 0.875rem;
    background: white;
}

.filter-select:focus {
    outline: none;
    border-color: var(--primary-blue);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Main Content Grid */
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.content-section {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-gray);
    overflow: hidden;
}

.section-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-gray);
    background: var(--secondary-gray);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
}

.section-icon {
    width: 24px;
    height: 24px;
    color: var(--orange-text);
}

.section-icon.blue {
    color: var(--primary-blue);
}

.section-body {
    padding: 1.5rem;
}

/* Stock Alert Items */
.stock-alert-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    margin-bottom: 1rem;
    background: white;
    border-radius: 8px;
    border: 1px solid var(--border-gray);
}

.product-image {
    width: 60px;
    height: 60px;
    margin-right: 1rem;
    flex-shrink: 0;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
    background: #f8fafc;
}

.product-info {
    flex: 1;
    min-width: 0;
}

.product-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.product-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
}

.priority-badges {
    display: flex;
    gap: 0.5rem;
}

.priority-badge {
    padding: 0.125rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 12px;
    text-transform: lowercase;
}

.priority-badge.high {
    background: var(--yellow-bg);
    color: var(--yellow-text);
}

.priority-badge.critical {
    background: var(--red-bg);
    color: var(--red-text);
}

.priority-badge.medium {
    background: var(--blue-bg);
    color: var(--blue-text);
}

.priority-badge.low {
    background: var(--green-bg);
    color: var(--green-text);
}

.status-badge {
    padding: 0.125rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 12px;
    text-transform: lowercase;
}

.status-badge.active {
    background: var(--red-bg);
    color: var(--red-text);
}

.status-badge.resolved {
    background: var(--green-bg);
    color: var(--green-text);
}

.product-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.25rem 1rem;
    margin-bottom: 0.75rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
}

.detail-label {
    color: var(--text-muted);
    font-weight: 400;
}

.detail-value {
    color: var(--text-dark);
    font-weight: 500;
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-left: 1rem;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid var(--border-gray);
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 80px;
    background: white;
    color: var(--text-muted);
}

.action-btn:hover {
    background: var(--secondary-gray);
    border-color: var(--primary-blue);
}

/* Reorder Suggestions */
.reorder-suggestion-item {
    padding: 1rem;
    margin-bottom: 1rem;
    background: white;
    border-radius: 8px;
    border: 1px solid var(--border-gray);
}

.suggestion-header {
    margin-bottom: 0.75rem;
}

.suggestion-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-dark);
    margin: 0;
}

.suggestion-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.25rem 1rem;
    margin-bottom: 1rem;
}

.create-order-section {
    border-top: 1px solid var(--border-gray);
    padding-top: 0.75rem;
}

.create-order-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.75rem;
    background: #1f2937;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s ease;
}

.create-order-btn:hover {
    background: #374151;
}

/* Alert Summary */
.summary-section {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border-gray);
    padding: 1.5rem;
}

.summary-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.summary-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 8px;
    background: var(--secondary-gray);
}

.summary-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.75rem;
    flex-shrink: 0;
}

.summary-dot.critical { background-color: var(--red-text); }
.summary-dot.high { background-color: var(--orange-text); }
.summary-dot.medium { background-color: var(--yellow-text); }
.summary-dot.resolved { background-color: var(--green-text); }

.summary-label {
    flex: 1;
    font-size: 0.875rem;
    color: var(--text-dark);
    margin-right: 0.5rem;
}

.summary-count {
    font-weight: 600;
    font-size: 1rem;
    color: var(--text-dark);
}

/* Responsive Design */
@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .alert-badges {
        margin-top: 1rem;
    }
}

/* Alert Animations */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* Medical Alert Notifications */
.medical-toast {
    background: white;
    border: 1px solid var(--border-gray);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-left: 4px solid var(--primary-blue);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--text-muted);
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close:hover {
    color: var(--text-dark);
}

.btn-close::before {
    content: '×';
}

/* Loading States */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: var(--text-muted);
}

.spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--border-gray);
    border-top: 3px solid var(--primary-blue);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Empty States */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
    text-align: center;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h6 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.empty-state p {
    font-size: 0.9rem;
    margin: 0;
    max-width: 300px;
}
</style>

<script>
const API_BASE = '{{ url("/api/v1") }}';
let alertsData = [];
let reordersData = [];

// Load dashboard data on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('🏥 Loading Medical Stock Dashboard...');
    loadDashboard();
});

async function loadDashboard() {
    console.log('� Fetching medical inventory data...');
    await Promise.all([
        loadAlerts(), 
        loadReorders()
    ]);
}

async function loadStats() {
    try {
        console.log('� Fetching statistics from:', `${API_BASE}/stock-alerts/stats`);
        const response = await fetch(`${API_BASE}/stock-alerts/stats`);
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            const statsHtml = `
                <div class="col-lg-3 col-md-6">
                    <div class="medical-stat-card">
                        <div class="stat-icon-wrapper critical">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">${data.critical_alerts}</div>
                            <div class="stat-label">Critical Alerts</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="medical-stat-card">
                        <div class="stat-icon-wrapper warning">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">${data.active_alerts}</div>
                            <div class="stat-label">Active Alerts</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="medical-stat-card">
                        <div class="stat-icon-wrapper info">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">${data.pending_reorders}</div>
                            <div class="stat-label">Pending Orders</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="medical-stat-card">
                        <div class="stat-icon-wrapper success">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">${data.low_stock_products}</div>
                            <div class="stat-label">Low Stock Items</div>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('stats-container').innerHTML = statsHtml;
            
            // Update alert count badges
            document.getElementById('alert-count').textContent = data.active_alerts;
            document.getElementById('critical-badge-count').textContent = data.critical_alerts;
        } else {
            throw new Error(result.message || 'Failed to load statistics');
        }
    } catch (error) {
        console.error('❌ Error loading stats:', error);
        document.getElementById('stats-container').innerHTML = `
            <div class="col-12">
                <div class="medical-stat-card">
                    <div class="medical-empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <h6>Unable to Load Statistics</h6>
                        <p>Please check your connection and try again.</p>
                    </div>
                </div>
            </div>
        `;
    }
}

async function loadAlerts(filters = {}) {
    try {
        // Build query parameters for RESTful API filtering
        const queryParams = new URLSearchParams();
        if (filters.status) queryParams.append('status', filters.status);
        if (filters.severity) queryParams.append('severity', filters.severity);
        
        const queryString = queryParams.toString();
        const url = `${API_BASE}/stock-alerts${queryString ? '?' + queryString : ''}`;
        
        console.log('🚨 Fetching alerts from:', url);
        const response = await fetch(url);
        
        // Check for HTTP errors first (RESTful approach)
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            alertsData = result.data;
            
            // Apply client-side search filtering if needed
            const searchTerm = document.getElementById('searchInput')?.value?.toLowerCase();
            let filteredAlerts = alertsData;
            
            if (searchTerm) {
                filteredAlerts = alertsData.filter(alert => 
                    (alert.product_name && alert.product_name.toLowerCase().includes(searchTerm)) ||
                    alert.product_id.toString().includes(searchTerm)
                );
            }
            
            renderAlerts(filteredAlerts);
        } else {
            alertsData = [];
            document.getElementById('alerts-container').innerHTML = `
                <div class="medical-empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h6>All Clear!</h6>
                    <p>No critical stock alerts at this time. All medical supplies are adequately stocked.</p>
                </div>
            `;
            // Reset all counts to 0 when no alerts
            updateAlertSummary([]);
        }
    } catch (error) {
        console.error('❌ Error loading alerts:', error);
        alertsData = [];
        document.getElementById('alerts-container').innerHTML = `
            <div class="medical-empty-state">
                <i class="fas fa-wifi"></i>
                <h6>Connection Error</h6>
                <p>Unable to load stock alerts: ${error.message}</p>
            </div>
        `;
        // Reset all counts to 0 on error
        updateAlertSummary([]);
    }
}

async function loadReorders(filters = {}) {
    try {
        // Build query parameters for RESTful API filtering
        const queryParams = new URLSearchParams();
        if (filters.status) queryParams.append('status', filters.status);
        if (filters.priority) queryParams.append('priority', filters.priority);
        
        const queryString = queryParams.toString();
        const url = `${API_BASE}/reorder-requests${queryString ? '?' + queryString : ''}`;
        
        console.log('🛒 Fetching reorder requests from:', url);
        const response = await fetch(url);
        
        // Check for HTTP errors first (RESTful approach)
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            reordersData = result.data;
            renderReorders(reordersData);
        } else {
            document.getElementById('reorders-container').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-clipboard-check"></i>
                    <h6>No Pending Orders</h6>
                    <p>All procurement requests are up to date. No immediate reorders required.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('❌ Error loading reorder requests:', error);
        document.getElementById('reorders-container').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-wifi"></i>
                <h6>Connection Error</h6>
                <p>Unable to load reorder requests: ${error.message}</p>
            </div>
        `;
    }
}

function renderAlerts(alerts) {
    if (!alerts || alerts.length === 0) {
        document.getElementById('alerts-container').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h6>All Clear!</h6>
                <p>No critical stock alerts at this time. All medical supplies are adequately stocked.</p>
            </div>
        `;
        return;
    }

    const alertsHtml = alerts.map(alert => `
        <div class="stock-alert-item">
            <div class="product-image">
                <img src="${getProductImage(alert.product_name || 'default')}" 
                     alt="${alert.product_name || 'Product'}" 
                     onerror="this.src='https://via.placeholder.com/60x60/f8fafc/64748b?text=MD'">
            </div>
            <div class="product-info">
                <div class="product-header">
                    <h6 class="product-name">${alert.product_name || 'Medical Item #' + alert.product_id}</h6>
                    <div class="priority-badges">
                        <span class="priority-badge ${alert.severity}">${alert.severity}</span>
                        <span class="status-badge ${alert.status}">${alert.status}</span>
                    </div>
                </div>
                <div class="product-details">
                    <div class="detail-row">
                        <span class="detail-label">SKU:</span>
                        <span class="detail-value">${alert.product_id}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Category:</span>
                        <span class="detail-value">Disposables</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Current:</span>
                        <span class="detail-value ${alert.current_quantity === 0 ? 'text-danger' : alert.current_quantity <= 5 ? 'text-warning' : ''}">${alert.current_quantity} units</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Min Required:</span>
                        <span class="detail-value">${alert.reorder_level} units</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Supplier:</span>
                        <span class="detail-value">SafeHands Inc.</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Alert Date:</span>
                        <span class="detail-value">${new Date(alert.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <button class="action-btn" onclick="resolveAlert(${alert.id})">
                    <i class="fas fa-check-circle"></i>
                    Resolve
                </button>
                <button class="action-btn" onclick="createReorderFromAlert(${alert.id})">
                    <i class="fas fa-redo"></i>
                    Reorder
                </button>
            </div>
        </div>
    `).join('');
    
    document.getElementById('alerts-container').innerHTML = alertsHtml;
    updateAlertSummary(alerts);
}

function renderReorders(reorders) {
    if (!reorders || reorders.length === 0) {
        document.getElementById('reorders-container').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <h6>No Pending Orders</h6>
                <p>All procurement requests are up to date. No immediate reorders required.</p>
            </div>
        `;
        return;
    }

    const reordersHtml = reorders.map(reorder => {
        // Calculate estimated cost based on quantity and product cost price
        const estimatedCost = (reorder.quantity || reorder.suggested_quantity || 50) * (reorder.product?.cost_price || 25);
        
        return `
            <div class="reorder-suggestion-item">
                <div class="suggestion-header">
                    <h6 class="suggestion-title">${reorder.product?.name || reorder.product_name || 'Medical Item #' + reorder.product_id}</h6>
                    <span class="priority-badge ${reorder.priority || 'medium'}">${reorder.priority || 'medium'}</span>
                </div>
                <div class="suggestion-details">
                    <div class="detail-row">
                        <span class="detail-label">Requested Qty:</span>
                        <span class="detail-value">${reorder.quantity || reorder.suggested_quantity || 50} units</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Current Stock:</span>
                        <span class="detail-value">${reorder.product?.quantity || 0} units</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Est. Cost:</span>
                        <span class="detail-value">$${estimatedCost.toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">${reorder.status || 'pending'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Requested:</span>
                        <span class="detail-value">${new Date(reorder.created_at).toLocaleDateString()}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Reason:</span>
                        <span class="detail-value">${reorder.reason || 'Stock level below minimum'}</span>
                    </div>
                </div>
                <div class="create-order-section">
                    ${reorder.status === 'pending' ? 
                        `<button class="create-order-btn" onclick="approveReorder(${reorder.id})">
                            <i class="fas fa-check"></i>
                            Approve Order
                        </button>` :
                        `<span class="status-badge ${reorder.status}">${reorder.status}</span>`
                    }
                </div>
            </div>
        `;
    }).join('');
    
    document.getElementById('reorders-container').innerHTML = reordersHtml;
}

function getProductImage(productName) {
    // Use placeholder service for medical product images
    const imageMap = {
        'surgical gloves': 'https://via.placeholder.com/60x60/f3f4f6/6b7280?text=SG',
        'antiseptic solution': 'https://via.placeholder.com/60x60/f3f4f6/6b7280?text=AS',
        'disposable syringes': 'https://via.placeholder.com/60x60/f3f4f6/6b7280?text=DS', 
        'face masks': 'https://via.placeholder.com/60x60/f3f4f6/6b7280?text=FM',
        default: 'https://via.placeholder.com/60x60/f3f4f6/6b7280?text=MD'
    };
    
    const key = productName.toLowerCase();
    for (const [product, image] of Object.entries(imageMap)) {
        if (key.includes(product.split(' ')[0])) {
            return image;
        }
    }
    return imageMap.default;
}

async function createReorderFromAlert(alertId) {
    try {
        // Find the alert data
        const alert = alertsData.find(a => a.id === alertId);
        if (!alert) {
            showMedicalAlert('danger', 'Error', 'Alert not found');
            return;
        }
        
        // Calculate suggested quantity (reorder level * 2, minimum 10)
        const suggestedQuantity = Math.max(alert.reorder_level * 2, 10);
        const estimatedCost = suggestedQuantity * 25; // Default cost per unit
        
        // Determine priority based on current stock
        let priority = 'medium';
        if (alert.current_quantity === 0) {
            priority = 'urgent';
        } else if (alert.current_quantity <= alert.reorder_level * 0.3) {
            priority = 'high';
        }
        
        // Create reorder request
        const response = await fetch(`${API_BASE}/reorder-requests`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: alert.product_id,
                current_quantity: alert.current_quantity,
                reorder_level: alert.reorder_level,
                suggested_quantity: suggestedQuantity,
                priority: priority,
                estimated_cost: estimatedCost,
                supplier: 'Auto-generated',
                notes: `Created from stock alert #${alertId}`
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMedicalAlert('success', 'Reorder Created', `Reorder request created for ${alert.product_name || 'Product #' + alert.product_id}`);
            await loadReorders(); // Refresh reorder requests
        } else {
            throw new Error(result.message || 'Failed to create reorder request');
        }
    } catch (error) {
        console.error('❌ Error creating reorder request:', error);
        showMedicalAlert('danger', 'Error', 'Failed to create reorder request: ' + error.message);
    }
}

function updateAlertSummary(alerts) {
    const counts = {
        critical: 0,
        high: 0,
        medium: 0,
        resolved: 0,
        active: 0
    };
    
    alerts.forEach(alert => {
        if (alert.status !== 'resolved') {
            counts.active++;
        }
        
        if (alert.severity === 'critical') counts.critical++;
        else if (alert.severity === 'high') counts.high++;
        else if (alert.severity === 'medium') counts.medium++;
        else if (alert.status === 'resolved') counts.resolved++;
    });
    
    // Update summary section counts
    document.getElementById('critical-count').textContent = counts.critical;
    document.getElementById('high-count').textContent = counts.high;
    document.getElementById('medium-count').textContent = counts.medium;
    document.getElementById('resolved-count').textContent = counts.resolved;
    
    // Update header badge counts
    document.getElementById('alert-count').textContent = counts.active;
    document.getElementById('critical-badge-count').textContent = counts.critical;
}

function getAlertIcon(alertType) {
    switch(alertType) {
        case 'low_stock': return 'box-open';
        case 'out_of_stock': return 'exclamation-triangle';
        case 'critical_low': return 'exclamation-circle';
        default: return 'bell';
    }
}

function getReorderIcon(priority) {
    switch(priority) {
        case 'urgent': return 'bolt';
        case 'high': return 'arrow-up';
        case 'medium': return 'minus';
        case 'low': return 'arrow-down';
        default: return 'shopping-cart';
    }
}

function getSeverityColor(severity) {
    switch(severity) {
        case 'critical': return 'danger';
        case 'high': return 'warning'; 
        case 'medium': return 'info';
        case 'low': return 'secondary';
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

function getMedicalActionButton(alert) {
    if (alert.status === 'active') {
        return `<button class="btn btn-medical-success btn-sm" onclick="acknowledgeAlert(${alert.id})">
                    <i class="fas fa-check me-1"></i>Acknowledge
                </button>`;
    } else if (alert.status === 'acknowledged') {
        return `<button class="btn btn-medical-primary btn-sm" onclick="resolveAlert(${alert.id})">
                    <i class="fas fa-check-double me-1"></i>Resolve
                </button>`;
    } else {
        return `<span class="medical-badge resolved">RESOLVED</span>`;
    }
}

async function applyFilters() {
    const priorityFilter = document.getElementById('priorityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    // Use RESTful API filtering - load alerts with server-side filters
    const filters = {};
    if (priorityFilter) filters.severity = priorityFilter;
    if (statusFilter) filters.status = statusFilter;
    
    await loadAlerts(filters);
}

// Debounced search function for better performance
let searchTimeout;
function debouncedSearch() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 300); // Wait 300ms after user stops typing
}

async function refreshDashboard() {
    showMedicalAlert('info', 'Refreshing Data', 'Updating medical inventory information...');
    await loadDashboard();
    showMedicalAlert('success', 'Data Updated', 'Medical inventory dashboard refreshed successfully');
}

async function triggerStockCheck() {
    try {
        console.log('🔍 Triggering medical stock check...');
        showMedicalAlert('info', 'Health Check', 'Running comprehensive stock level analysis...');
        
        const response = await fetch(`${API_BASE}/stock-alerts/trigger-check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMedicalAlert('success', 'Health Check Complete', result.message);
            await refreshDashboard();
        } else {
            throw new Error(result.message || 'Health check failed');
        }
    } catch (error) {
        console.error('❌ Error triggering stock check:', error);
        showMedicalAlert('danger', 'Health Check Failed', 'Unable to complete stock analysis: ' + error.message);
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
            showMedicalAlert('success', 'Alert Acknowledged', 'Stock alert has been acknowledged by medical staff');
            await loadAlerts();
        } else {
            throw new Error(result.message || 'Failed to acknowledge alert');
        }
    } catch (error) {
        console.error('❌ Error acknowledging alert:', error);
        showMedicalAlert('danger', 'Error', 'Failed to acknowledge alert: ' + error.message);
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
            showMedicalAlert('success', 'Alert Resolved', 'Stock alert has been successfully resolved');
            await loadAlerts();
        } else {
            throw new Error(result.message || 'Failed to resolve alert');
        }
    } catch (error) {
        console.error('❌ Error resolving alert:', error);
        showMedicalAlert('danger', 'Error', 'Failed to resolve alert: ' + error.message);
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
            showMedicalAlert('success', 'Order Approved', 'Procurement request approved and inventory updated');
            await loadReorders();
            await loadAlerts(); // Refresh alerts as they might be auto-resolved
        } else {
            throw new Error(result.message || 'Failed to approve reorder');
        }
    } catch (error) {
        console.error('❌ Error approving reorder:', error);
        showMedicalAlert('danger', 'Error', 'Failed to approve reorder: ' + error.message);
    }
}

function showMedicalAlert(type, title, message) {
    // Create alert container if it doesn't exist
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        document.body.appendChild(alertContainer);
    }
    
    const alertId = Date.now();
    
    const typeColors = {
        'success': '#16a34a',
        'info': '#3b82f6',
        'warning': '#d97706',
        'danger': '#dc2626'
    };
    
    const alertHtml = `
        <div class="medical-toast" id="alert-${alertId}" style="
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            border-left: 4px solid ${typeColors[type]};
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease-out;
        ">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <strong style="color: ${typeColors[type]}; font-size: 0.9rem;">${title}:</strong>
                    <div style="color: #1e293b; margin-top: 0.25rem; font-size: 0.85rem;">${message}</div>
                </div>
                <button type="button" style="
                    background: none;
                    border: none;
                    color: #64748b;
                    cursor: pointer;
                    font-size: 1.1rem;
                    line-height: 1;
                    padding: 0;
                    margin-left: 1rem;
                " onclick="this.parentElement.parentElement.remove()">×</button>
            </div>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alertElement = document.getElementById(`alert-${alertId}`);
        if (alertElement) {
            alertElement.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => alertElement.remove(), 300);
        }
    }, 5000);
}

function exportAlerts() {
    showMedicalAlert('info', 'Export Report', 'Medical inventory report export feature will be available soon');
}

function createReorderRequest() {
    showMedicalAlert('info', 'New Request', 'Manual procurement request creation will be available soon');
}

function viewAlertDetails(alertId) {
    showMedicalAlert('info', 'Alert Details', `Viewing details for alert #${alertId}`);
}

function createOrder(reorderId) {
    showMedicalAlert('info', 'Create Order', `Creating purchase order for reorder #${reorderId}`);
}

// Enhanced console logging for medical theme
console.log('%c🏥 Medical Stock Management System', 'color: #26a69a; font-size: 16px; font-weight: bold;');
console.log('%cReal-time medical inventory monitoring with Observer Pattern', 'color: #546e7a; font-size: 12px;');
console.log('%cAPI Base URL: ' + API_BASE, 'color: #66bb6a; font-size: 12px;');
</script>
@endsection
