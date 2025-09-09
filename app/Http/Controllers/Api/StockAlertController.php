<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockAlert;
use App\Models\Product;
use App\Models\ReorderRequest;
use App\Observers\StockSubject;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockAlertController extends Controller
{
    /**
     * Display a listing of stock alerts
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockAlert::with('product:product_id,name');
        
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
        
        // Order by severity and creation date
        $alerts = $query->orderByRaw("
            CASE severity 
                WHEN 'critical' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'medium' THEN 3 
                WHEN 'low' THEN 4 
            END
        ")->orderBy('created_at', 'desc')->get();
        
        // Add product name to the response
        $alerts->each(function ($alert) {
            $alert->product_name = $alert->product ? $alert->product->name : null;
            unset($alert->product);
        });
        
        return response()->json([
            'success' => true,
            'data' => $alerts,
            'total' => $alerts->count()
        ]);
    }
    
    /**
     * Store a newly created stock alert
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'alert_type' => 'required|in:low_stock,out_of_stock,expired',
            'message' => 'required|string',
            'current_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'severity' => 'required|in:low,medium,high,critical'
        ]);
        
        $alert = StockAlert::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Stock alert created successfully',
            'data' => $alert
        ], 201);
    }
    
    /**
     * Display the specified stock alert
     */
    public function show(string $id): JsonResponse
    {
        $alert = StockAlert::with('product:product_id,name')->findOrFail($id);
        
        $alert->product_name = $alert->product ? $alert->product->name : null;
        unset($alert->product);
        
        return response()->json([
            'success' => true,
            'data' => $alert
        ]);
    }
    
    /**
     * Update the specified stock alert
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $alert = StockAlert::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'sometimes|in:active,acknowledged,resolved',
            'severity' => 'sometimes|in:low,medium,high,critical',
            'message' => 'sometimes|string'
        ]);
        
        $alert->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Stock alert updated successfully',
            'data' => $alert
        ]);
    }
    
    /**
     * Acknowledge a stock alert
     */
    public function acknowledge(string $id): JsonResponse
    {
        $alert = StockAlert::findOrFail($id);
        
        $alert->update([
            'status' => 'acknowledged',
            'acknowledged_by' => 1, // In real app, use auth()->id()
            'acknowledged_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stock alert acknowledged successfully',
            'data' => $alert
        ]);
    }
    
    /**
     * Resolve a stock alert
     */
    public function resolve(string $id): JsonResponse
    {
        $alert = StockAlert::findOrFail($id);
        
        $alert->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Stock alert resolved successfully',
            'data' => $alert
        ]);
    }
    
    /**
     * Trigger stock level check for all products
     */
    public function triggerStockCheck(): JsonResponse
    {
        $products = Product::all();
        
        $alertsCreated = 0;
        $reordersCreated = 0;
        
        foreach ($products as $product) {
            // Check if stock is below reorder level
            if ($product->quantity <= $product->reorder_level) {
                // Check if alert already exists for this product
                $existingAlert = StockAlert::where('product_id', $product->product_id)
                    ->where('status', 'active')
                    ->first();
                
                if (!$existingAlert) {
                    // Determine severity based on how low the stock is
                    $severity = 'medium';
                    if ($product->quantity == 0) {
                        $severity = 'critical';
                    } elseif ($product->quantity <= ($product->reorder_level * 0.5)) {
                        $severity = 'high';
                    }
                    
                    // Create stock alert
                    StockAlert::create([
                        'product_id' => $product->product_id,
                        'alert_type' => $product->quantity == 0 ? 'out_of_stock' : 'low_stock',
                        'message' => $product->quantity == 0 
                            ? "Product '{$product->name}' is out of stock"
                            : "Product '{$product->name}' is running low on stock",
                        'current_quantity' => $product->quantity,
                        'reorder_level' => $product->reorder_level,
                        'severity' => $severity
                    ]);
                    
                    $alertsCreated++;
                }
                
                // Check if auto reorder is enabled and no pending reorder exists
                if ($product->auto_reorder ?? true) {
                    $existingReorder = ReorderRequest::where('product_id', $product->product_id)
                        ->whereIn('status', ['pending', 'approved', 'ordered'])
                        ->first();
                    
                    if (!$existingReorder) {
                        // Calculate suggested quantity (reorder level * 2)
                        $suggestedQuantity = $product->reorder_level * 2;
                        
                        // Determine priority
                        $priority = 'medium';
                        if ($product->quantity == 0) {
                            $priority = 'urgent';
                        } elseif ($product->quantity <= ($product->reorder_level * 0.3)) {
                            $priority = 'high';
                        }
                        
                        // Create reorder request
                        ReorderRequest::create([
                            'product_id' => $product->product_id,
                            'current_quantity' => $product->quantity,
                            'reorder_level' => $product->reorder_level,
                            'suggested_quantity' => $suggestedQuantity,
                            'priority' => $priority,
                            'estimated_cost' => ($product->cost_price ?? 0) * $suggestedQuantity,
                            'supplier' => $product->supplier,
                            'notes' => "Auto-generated reorder request due to low stock"
                        ]);
                        
                        $reordersCreated++;
                    }
                }
                
                // For now, skip Observer pattern to avoid logging issues
                // The Observer pattern is implemented but has logging conflicts
                // $stockSubject = new StockSubject($product);
                // $stockSubject->attach(new \App\Observers\StockAlertObserver());
                // $stockSubject->attach(new \App\Observers\ReorderRequestObserver());
                // $stockSubject->notify();
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Stock check completed. Created {$alertsCreated} alerts and {$reordersCreated} reorder requests.",
            'data' => [
                'alerts_created' => $alertsCreated,
                'reorders_created' => $reordersCreated,
                'products_checked' => $products->count()
            ]
        ]);
    }
    
    /**
     * Get stock alert statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_alerts' => StockAlert::count(),
            'active_alerts' => StockAlert::where('status', 'active')->count(),
            'acknowledged_alerts' => StockAlert::where('status', 'acknowledged')->count(),
            'resolved_alerts' => StockAlert::where('status', 'resolved')->count(),
            'critical_alerts' => StockAlert::where('severity', 'critical')->where('status', '!=', 'resolved')->count(),
            'high_alerts' => StockAlert::where('severity', 'high')->where('status', '!=', 'resolved')->count(),
            'medium_alerts' => StockAlert::where('severity', 'medium')->where('status', '!=', 'resolved')->count(),
            'low_alerts' => StockAlert::where('severity', 'low')->where('status', '!=', 'resolved')->count(),
            'low_stock_products' => Product::whereColumn('quantity', '<=', 'reorder_level')->count(),
            'out_of_stock_products' => Product::where('quantity', 0)->count(),
            'pending_reorders' => ReorderRequest::where('status', 'pending')->count()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Remove the specified stock alert
     */
    public function destroy(string $id): JsonResponse
    {
        $alert = StockAlert::findOrFail($id);
        $alert->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Stock alert deleted successfully'
        ]);
    }
}
