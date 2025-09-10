<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use App\Models\ReorderRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockAlertController extends Controller
{
    /**
     * Display all stock alerts for admin dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockAlert::with(['product' => function($query) {
            $query->select('product_id', 'name', 'sku', 'quantity', 'reorder_level', 'supplier');
        }]);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity if provided
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by alert type if provided
        if ($request->has('alert_type')) {
            $query->where('alert_type', $request->alert_type);
        }

        $alerts = $query->orderBy('severity', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $alerts,
            'summary' => [
                'total_alerts' => StockAlert::count(),
                'active_alerts' => StockAlert::where('status', 'active')->count(),
                'critical_alerts' => StockAlert::where('severity', 'critical')->where('status', 'active')->count(),
                'high_alerts' => StockAlert::where('severity', 'high')->where('status', 'active')->count(),
            ]
        ]);
    }

    /**
     * Acknowledge a stock alert
     */
    public function acknowledge(Request $request, $id): JsonResponse
    {
        $alert = StockAlert::findOrFail($id);
        
        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $request->user_id ?? 1, // In real app, get from auth
            'acknowledged_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert acknowledged successfully',
            'data' => $alert->load('product')
        ]);
    }

    /**
     * Resolve a stock alert
     */
    public function resolve(Request $request, $id): JsonResponse
    {
        $alert = StockAlert::findOrFail($id);
        
        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert resolved successfully',
            'data' => $alert->load('product')
        ]);
    }

    /**
     * Get all reorder requests
     */
    public function reorderRequests(Request $request): JsonResponse
    {
        $query = ReorderRequest::with(['product' => function($query) {
            $query->select('product_id', 'name', 'sku', 'quantity', 'reorder_level', 'supplier', 'cost');
        }]);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority if provided
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->orderBy('priority', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests,
            'summary' => [
                'total_requests' => ReorderRequest::count(),
                'pending_requests' => ReorderRequest::where('status', 'pending')->count(),
                'urgent_requests' => ReorderRequest::where('priority', 'urgent')->where('status', 'pending')->count(),
            ]
        ]);
    }

    /**
     * Approve a reorder request
     */
    public function approveReorder(Request $request, $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::findOrFail($id);
        
        $reorderRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user_id ?? 1, // In real app, get from auth
            'approved_at' => now(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reorder request approved successfully',
            'data' => $reorderRequest->load('product')
        ]);
    }

    /**
     * Get dashboard summary with low stock products
     */
    public function dashboard(): JsonResponse
    {
        // Get products with low stock
        $lowStockProducts = Product::whereRaw('quantity <= reorder_level')
                                  ->where('is_active', true)
                                  ->with(['stockAlerts' => function($query) {
                                      $query->where('status', 'active');
                                  }])
                                  ->get();

        // Get recent alerts
        $recentAlerts = StockAlert::with('product')
                                 ->where('created_at', '>=', now()->subDays(7))
                                 ->orderBy('created_at', 'desc')
                                 ->limit(10)
                                 ->get();

        // Get pending reorder requests
        $pendingReorders = ReorderRequest::with('product')
                                        ->where('status', 'pending')
                                        ->orderBy('priority', 'desc')
                                        ->limit(10)
                                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'low_stock_products' => $lowStockProducts,
                'recent_alerts' => $recentAlerts,
                'pending_reorders' => $pendingReorders,
                'summary' => [
                    'total_low_stock' => $lowStockProducts->count(),
                    'active_alerts' => StockAlert::where('status', 'active')->count(),
                    'pending_reorders' => ReorderRequest::where('status', 'pending')->count(),
                    'out_of_stock' => Product::where('quantity', 0)->where('is_active', true)->count(),
                ]
            ]
        ]);
    }

    /**
     * Trigger stock check for all products (for testing)
     */
    public function triggerStockCheck(): JsonResponse
    {
        $products = Product::where('is_active', true)->get();
        $alertsCreated = 0;
        $reordersCreated = 0;

        foreach ($products as $product) {
            if ($product->isLowStock()) {
                // Create alert if none exists
                $existingAlert = StockAlert::where('product_id', $product->product_id)
                                          ->where('status', 'active')
                                          ->first();

                if (!$existingAlert) {
                    StockAlert::create([
                        'product_id' => $product->product_id,
                        'alert_type' => $product->quantity <= 0 ? 'out_of_stock' : 'low_stock',
                        'message' => $product->quantity <= 0 
                            ? "Product {$product->name} is out of stock" 
                            : "Product {$product->name} is running low (Qty: {$product->quantity}, Reorder Level: {$product->reorder_level})",
                        'current_quantity' => $product->quantity,
                        'reorder_level' => $product->reorder_level,
                        'severity' => $product->quantity <= 0 ? 'critical' : ($product->quantity <= $product->reorder_level * 0.5 ? 'high' : 'medium')
                    ]);
                    $alertsCreated++;
                }

                // Create reorder request if auto_reorder is enabled
                if ($product->auto_reorder) {
                    $existingRequest = ReorderRequest::where('product_id', $product->product_id)
                                                    ->where('status', 'pending')
                                                    ->first();

                    if (!$existingRequest) {
                        $suggestedQuantity = max(($product->reorder_level * 2), 50); // Order enough for buffer
                        
                        ReorderRequest::create([
                            'product_id' => $product->product_id,
                            'current_quantity' => $product->quantity,
                            'reorder_level' => $product->reorder_level,
                            'suggested_quantity' => $suggestedQuantity,
                            'priority' => $product->quantity <= 0 ? 'urgent' : 'medium',
                            'estimated_cost' => $product->cost ? ($product->cost * $suggestedQuantity) : null,
                            'supplier' => $product->supplier
                        ]);
                        $reordersCreated++;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock check completed',
            'data' => [
                'alerts_created' => $alertsCreated,
                'reorders_created' => $reordersCreated,
                'products_checked' => $products->count()
            ]
        ]);
    }
}
