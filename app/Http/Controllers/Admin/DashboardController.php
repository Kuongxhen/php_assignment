<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        return view('admin.dashboard.dashboard');
    }
    
    /**
     * Display the stock alerts dashboard
     */
    public function stockDashboard()
    {
        return view('admin.dashboard.stock-dashboard');
    }
}
