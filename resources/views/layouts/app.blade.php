<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'My Project') }}</title>

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Navigation Styles -->
    <style>
        /* Navbar transparency and glassmorphism effect */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: all 0.3s ease;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar.scrolled {
            background: rgba(33, 37, 41, 0.98) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
        }
        
        body {
            background-color: var(--bs-gray-50);
            transition: background-color 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0 0.25rem;
            transition: all 0.2s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff !important;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 0.75rem;
            padding: 0.75rem 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .dropdown-header {
            font-weight: 600;
            color: #6c757d;
            padding: 0.5rem 1.5rem 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .badge {
            font-size: 0.65rem;
            font-weight: 600;
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #e9ecef;
        }
        
        body.dark-mode .card {
            background-color: #2d2d2d;
            border-color: #404040;
        }
        
        /* Breadcrumb styles */
        .custom-breadcrumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .custom-breadcrumb .breadcrumb {
            margin: 0;
        }
        
        .custom-breadcrumb .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
        
        .custom-breadcrumb .breadcrumb-item a:hover {
            color: white;
        }
        
        .custom-breadcrumb .breadcrumb-item.active {
            color: white;
        }
        
        /* Status indicators */
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }
        
        .status-online { background-color: #28a745; }
        .status-warning { background-color: #ffc107; }
        .status-offline { background-color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4" style="background: rgba(33, 37, 41, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="fas fa-cube me-2"></i>{{ config('app.name', 'Inventory System') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Main Navigation -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="fas fa-boxes me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payment.*') ? 'active' : '' }}" href="{{ route('payment.index') }}">
                            <i class="fas fa-credit-card me-1"></i>Payments
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-shield-alt me-1"></i>Admin Panel
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Product Management</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                <i class="fas fa-boxes me-2"></i>Manage Products
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.products.create') }}">
                                <i class="fas fa-plus me-2"></i>Add Product
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Stock Management</h6></li>
                            <li><a class="dropdown-item" href="{{ route('admin.stock.dashboard') }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>Stock Alerts
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">API & Reports</h6></li>
                            <li><a class="dropdown-item" href="{{ url('/api/v1/health') }}" target="_blank">
                                <i class="fas fa-heartbeat me-2"></i>API Health
                            </a></li>
                            <li><a class="dropdown-item" href="{{ url('/api/v1/stock-alerts/stats') }}" target="_blank">
                                <i class="fas fa-chart-line me-2"></i>API Stats
                            </a></li>
                        </ul>
                    </li>
                </ul>
                
                <!-- Right Side Navigation -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="apiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-code me-1"></i>API
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="apiDropdown">
                            <li><h6 class="dropdown-header">REST API Endpoints</h6></li>
                            <li><a class="dropdown-item small" href="{{ url('/api/v1/products') }}" target="_blank">
                                <span class="badge bg-success me-2">GET</span>Products
                            </a></li>
                            <li><a class="dropdown-item small" href="{{ url('/api/v1/stock-alerts') }}" target="_blank">
                                <span class="badge bg-success me-2">GET</span>Stock Alerts
                            </a></li>
                            <li><a class="dropdown-item small" href="{{ url('/api/v1/reorder-requests') }}" target="_blank">
                                <span class="badge bg-success me-2">GET</span>Reorder Requests
                            </a></li>
                            <li><a class="dropdown-item small" href="{{ url('/api/v1/payments') }}" target="_blank">
                                <span class="badge bg-success me-2">GET</span>Payments
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ url('/api/documentation') }}" target="_blank">
                                <i class="fas fa-book me-2"></i>API Documentation
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Section -->
    @if(!request()->routeIs('welcome'))
    <div class="custom-breadcrumb">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    @if(request()->routeIs('products.*'))
                        <li class="breadcrumb-item active">
                            <i class="fas fa-boxes me-1"></i>Products
                        </li>
                    @elseif(request()->routeIs('payment.*'))
                        <li class="breadcrumb-item active">
                            <i class="fas fa-credit-card me-1"></i>Payments
                        </li>
                    @elseif(request()->routeIs('admin.*'))
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-shield-alt me-1"></i>Admin</a>
                        </li>
                        @if(request()->routeIs('admin.stock.*'))
                            <li class="breadcrumb-item active">
                                <i class="fas fa-exclamation-triangle me-1"></i>Stock Management
                            </li>
                        @else
                            <li class="breadcrumb-item active">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </li>
                        @endif
                    @endif
                </ol>
            </nav>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <div class="container-fluid">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Enhanced Navigation JavaScript -->
    <script>
        // Theme Toggle Functionality
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('themeIcon');
            
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                body.classList.add('dark-mode');
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme');
            const themeIcon = document.getElementById('themeIcon');
            
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                if (themeIcon) themeIcon.className = 'fas fa-sun';
            }
            
            // Add active states to navigation
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
            
            // Check API health status
            checkApiHealth();
            
            // Auto-refresh API status every 30 seconds
            setInterval(checkApiHealth, 30000);
            
            // Enhanced navbar transparency on scroll
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });

        // API Health Check
        async function checkApiHealth() {
            try {
                const response = await fetch('/api/v1/health', { 
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                
                const apiDropdown = document.getElementById('apiDropdown');
                if (apiDropdown) {
                    const indicator = document.createElement('span');
                    indicator.className = 'status-indicator status-online ms-1';
                    indicator.title = 'API Online';
                    
                    // Remove existing indicator
                    const existingIndicator = apiDropdown.querySelector('.status-indicator');
                    if (existingIndicator) {
                        existingIndicator.remove();
                    }
                    
                    if (response.ok) {
                        indicator.className = 'status-indicator status-online ms-1';
                        indicator.title = 'API Online';
                    } else {
                        indicator.className = 'status-indicator status-warning ms-1';
                        indicator.title = 'API Warning';
                    }
                    
                    apiDropdown.appendChild(indicator);
                }
            } catch (error) {
                console.warn('API health check failed:', error);
                const apiDropdown = document.getElementById('apiDropdown');
                if (apiDropdown) {
                    const indicator = document.createElement('span');
                    indicator.className = 'status-indicator status-offline ms-1';
                    indicator.title = 'API Offline';
                    
                    // Remove existing indicator
                    const existingIndicator = apiDropdown.querySelector('.status-indicator');
                    if (existingIndicator) {
                        existingIndicator.remove();
                    }
                    
                    apiDropdown.appendChild(indicator);
                }
            }
        }

        // Smooth transitions for dropdown items
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });

        // Navigation highlighting based on current route
        function highlightActiveNavigation() {
            const currentRoute = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                
                if (href && currentRoute.startsWith(href.replace(window.location.origin, ''))) {
                    link.classList.add('active');
                }
            });
        }

        // Call highlight function on page load
        document.addEventListener('DOMContentLoaded', highlightActiveNavigation);

        // Show loading states for navigation links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link, .dropdown-item');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.href && !this.href.includes('#') && !this.target === '_blank') {
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Loading...';
                        
                        // Reset after a delay if page doesn't change
                        setTimeout(() => {
                            this.innerHTML = originalText;
                        }, 2000);
                    }
                });
            });
        });

        // Console welcome message
        console.log('%c🚀 Inventory Management System', 'color: #667eea; font-size: 16px; font-weight: bold;');
        console.log('%cRESTful API enabled with Observer Pattern implementation', 'color: #666; font-size: 12px;');
        console.log('%cAPI Base URL: ' + window.location.origin + '/api/v1', 'color: #28a745; font-size: 12px;');
    </script>
    
    @stack('scripts')
</body>
</html>


