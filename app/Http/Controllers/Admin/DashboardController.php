<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use App\Models\Product;
use App\Models\ReorderRequest;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        // Dashboard statistics
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'active_alerts' => StockAlert::where('status', '!=', 'resolved')->count(),
            'critical_alerts' => StockAlert::where('severity', 'critical')->where('status', '!=', 'resolved')->count(),
            'pending_reorders' => ReorderRequest::where('status', 'pending')->count(),
            'total_staff' => User::where('role', '!=', 'patient')->count(),
        ];

        // Recent alerts
        $recentAlerts = StockAlert::with('product')
            ->where('status', '!=', 'resolved')
            ->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent reorder requests
        $recentReorders = ReorderRequest::with('product')
            ->where('status', 'pending')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAlerts', 'recentReorders'));
    }
}
