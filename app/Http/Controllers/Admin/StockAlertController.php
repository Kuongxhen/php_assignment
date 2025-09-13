<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use App\Models\ReorderRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class StockAlertController extends Controller
{
    /**        return redirect()->back()->with('success', "Stock check completed. Created {$alertsCreated} alerts and {$reordersCreated} reorder requests.");
    }
}     * Display stock alerts dashboard with reorder requests
     */
    public function index(Request $request)
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        // Get filter parameters
        $status = $request->get('status', 'all');
        $severity = $request->get('severity', 'all');
        $reorderStatus = $request->get('reorder_status', 'all');

        // Build alerts query with filters
        $alertsQuery = StockAlert::with('product');

        if ($status !== 'all') {
            $alertsQuery->where('status', $status);
        }

        if ($severity !== 'all') {
            $alertsQuery->where('severity', $severity);
        }

        $alerts = $alertsQuery->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get existing reorder requests for the displayed alerts
        $alertProductIds = $alerts->pluck('product_id')->toArray();
        $existingReorders = ReorderRequest::whereIn('product_id', $alertProductIds)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('product_id')
            ->toArray();

        // Build reorder requests query with filters
        $reorderQuery = ReorderRequest::with('product');

        if ($reorderStatus !== 'all') {
            $reorderQuery->where('status', $reorderStatus);
        }

        $reorderRequests = $reorderQuery->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'reorders');

        $stats = [
            'total_alerts' => StockAlert::count(),
            'active_alerts' => StockAlert::where('status', 'active')->count(),
            'acknowledged_alerts' => StockAlert::where('status', 'acknowledged')->count(),
            'resolved_alerts' => StockAlert::where('status', 'resolved')->count(),
            'critical_alerts' => StockAlert::where('severity', 'critical')->count(),
            'pending_reorders' => ReorderRequest::where('status', 'pending')->count(),
            'approved_reorders' => ReorderRequest::where('status', 'approved')->count(),
            'total_reorders' => ReorderRequest::count(),
        ];

        return view('admin.stock-alerts.index', compact('alerts', 'reorderRequests', 'stats', 'existingReorders', 'status', 'severity', 'reorderStatus'));
    }

    /**
     * Acknowledge a stock alert
     */
    public function acknowledge($id): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $alert = StockAlert::findOrFail($id);
        
        if ($alert->status === 'active') {
            $alert->update([
                'status' => 'acknowledged',
                'acknowledged_at' => now(),
                'acknowledged_by' => session('user')->staffId ?? null
            ]);

            return redirect()->back()->with('success', 'Alert acknowledged successfully.');
        }

        return redirect()->back()->with('info', 'Alert is already acknowledged or resolved.');
    }

    /**
     * Resolve a stock alert
     */
    public function resolve($id): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $alert = StockAlert::findOrFail($id);
        
        if ($alert->status !== 'resolved') {
            $alert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Alert resolved successfully.');
        }

        return redirect()->back()->with('info', 'Alert is already resolved.');
    }

    /**
     * Create a reorder request from a stock alert
     */
    public function createReorderRequest($alertId): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $alert = StockAlert::with('product')->findOrFail($alertId);
        
        // Check if there's already a pending reorder for this product
        $existingReorder = ReorderRequest::where('product_id', $alert->product_id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingReorder) {
            return redirect()->back()->with('info', 'There is already a pending reorder request for this product.');
        }

        // Calculate suggested quantity (bring stock to 3x reorder level)
        $suggestedQuantity = max($alert->reorder_level * 3 - $alert->current_quantity, $alert->reorder_level);

        // Determine priority based on alert severity
        $priority = 'medium';
        if ($alert->severity === 'critical') {
            $priority = 'urgent';
        } elseif ($alert->severity === 'high') {
            $priority = 'high';
        }

        // Create reorder request
        ReorderRequest::create([
            'product_id' => $alert->product_id,
            'current_quantity' => $alert->current_quantity,
            'reorder_level' => $alert->reorder_level,
            'suggested_quantity' => $suggestedQuantity,
            'priority' => $priority,
            'status' => 'pending',
            'estimated_cost' => $suggestedQuantity * ($alert->product->cost ?? 0),
            'supplier' => $alert->product->supplier ?? 'TBD',
            'notes' => "Auto-generated from stock alert #{$alert->id}",
            'requested_by' => session('user')->staffId ?? null,
        ]);

        return redirect()->back()->with('success', 'Reorder request created successfully from stock alert.');
    }

    /**
     * Filter alerts by status
     */
    public function filter(Request $request)
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $status = $request->get('status', 'all');
        $severity = $request->get('severity', 'all');

        $query = StockAlert::with('product');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($severity !== 'all') {
            $query->where('severity', $severity);
        }

        $alerts = $query->orderBy('severity', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get recent reorder requests (unchanged by filters)
        $reorderRequests = ReorderRequest::with('product')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'reorders');

        $stats = [
            'total_alerts' => StockAlert::count(),
            'active_alerts' => StockAlert::where('status', 'active')->count(),
            'acknowledged_alerts' => StockAlert::where('status', 'acknowledged')->count(),
            'resolved_alerts' => StockAlert::where('status', 'resolved')->count(),
            'critical_alerts' => StockAlert::where('severity', 'critical')->count(),
            'pending_reorders' => ReorderRequest::where('status', 'pending')->count(),
            'approved_reorders' => ReorderRequest::where('status', 'approved')->count(),
            'total_reorders' => ReorderRequest::count(),
        ];

        return view('admin.stock-alerts.index', compact('alerts', 'reorderRequests', 'stats'));
    }

    /**
     * Trigger stock check for all products and create missing alerts
     */
    public function triggerStockCheck(): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $products = Product::where('is_active', true)->get();
        $alertsCreated = 0;
        $reordersCreated = 0;

        foreach ($products as $product) {
            // Check if product is low on stock
            $isLowStock = $product->quantity <= ($product->reorder_level ?? 10);
            
            if ($isLowStock) {
                // Create alert if none exists for today
                $existingAlert = StockAlert::where('product_id', $product->product_id)
                                          ->where('status', 'active')
                                          ->whereDate('created_at', today())
                                          ->first();

                if (!$existingAlert) {
                    $severity = 'medium';
                    if ($product->quantity <= 0) {
                        $severity = 'critical';
                    } elseif ($product->quantity <= ($product->reorder_level ?? 10) * 0.5) {
                        $severity = 'high';
                    }

                    StockAlert::create([
                        'product_id' => $product->product_id,
                        'alert_type' => $product->quantity <= 0 ? 'out_of_stock' : 'low_stock',
                        'message' => $product->quantity <= 0 
                            ? "Product {$product->name} is out of stock" 
                            : "Low stock alert: {$product->name} has only {$product->quantity} units remaining (Reorder level: {$product->reorder_level})",
                        'current_quantity' => $product->quantity,
                        'reorder_level' => $product->reorder_level ?? 10,
                        'severity' => $severity,
                        'status' => 'active'
                    ]);
                    $alertsCreated++;
                }

                // Create reorder request if auto_reorder is enabled
                if ($product->auto_reorder) {
                    $existingRequest = ReorderRequest::where('product_id', $product->product_id)
                                                    ->whereIn('status', ['pending', 'approved'])
                                                    ->first();

                    if (!$existingRequest) {
                        $suggestedQuantity = max(($product->reorder_level ?? 10) * 3 - $product->quantity, ($product->reorder_level ?? 10));
                        
                        ReorderRequest::create([
                            'product_id' => $product->product_id,
                            'current_quantity' => $product->quantity,
                            'reorder_level' => $product->reorder_level ?? 10,
                            'suggested_quantity' => $suggestedQuantity,
                            'priority' => $product->quantity <= 0 ? 'urgent' : 'medium',
                            'status' => 'pending',
                            'estimated_cost' => ($product->cost ?? 0) * $suggestedQuantity,
                            'supplier' => $product->supplier ?? 'TBD',
                            'notes' => 'Auto-generated from stock check',
                            'requested_by' => session('user')->staffId ?? null,
                        ]);
                        $reordersCreated++;
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Stock check completed. Created {$alertsCreated} alerts and {$reordersCreated} reorder requests.");
    }

    /**
     * Approve a reorder request and update product stock
     */
    public function approveReorder($id): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $reorderRequest = ReorderRequest::with('product')->findOrFail($id);
        
        if ($reorderRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reorder requests can be approved.');
        }

        // Update reorder request status
        $reorderRequest->update([
            'status' => 'approved',
            'approved_by' => session('user')->staffId ?? null,
            'approved_at' => now(),
        ]);

        // Update product stock
        if ($reorderRequest->product) {
            $newQuantity = $reorderRequest->product->quantity + $reorderRequest->suggested_quantity;
            
            $reorderRequest->product->update([
                'quantity' => $newQuantity
            ]);

            // Auto-resolve related stock alerts if stock is now above reorder level
            if ($newQuantity > $reorderRequest->product->reorder_level) {
                StockAlert::where('product_id', $reorderRequest->product_id)
                    ->where('status', 'active')
                    ->update([
                        'status' => 'resolved',
                        'resolved_at' => now(),
                    ]);
            }
        }

        return redirect()->back()->with('success', "Reorder request approved successfully. Product stock updated to {$newQuantity} units.");
    }

    /**
     * Cancel a reorder request
     */
    public function cancelReorder($id): RedirectResponse
    {
        if (!session('user') || session('user_role') !== 'admin') {
            return redirect()->route('staffmod.login')->with('error', 'Please login as an administrator.');
        }

        $reorderRequest = ReorderRequest::findOrFail($id);
        
        if (!in_array($reorderRequest->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', 'Only pending or approved reorder requests can be cancelled.');
        }

        $reorderRequest->update([
            'status' => 'cancelled',
            'cancelled_by' => session('user')->staffId ?? null,
            'cancelled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Reorder request cancelled successfully.');
    }
}
