<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReorderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReorderRequestController extends Controller
{
    /**
     * Display a listing of reorder requests
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReorderRequest::with('product:product_id,name,cost_price,cost');
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority if provided
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Order by priority and creation date
        $reorderRequests = $query->orderByRaw("
            CASE priority 
                WHEN 'urgent' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'medium' THEN 3 
                WHEN 'low' THEN 4 
            END
        ")->orderBy('created_at', 'desc')->get();
        
        // Add product name and calculate estimated cost
        $reorderRequests->each(function ($reorderRequest) {
            $reorderRequest->product_name = $reorderRequest->product ? $reorderRequest->product->name : null;
            // Force recalculation of estimated cost by accessing the accessor
            $estimatedCost = $reorderRequest->estimated_cost;
            // Keep the product relationship for cost calculation
        });
        
        return response()->json([
            'success' => true,
            'data' => $reorderRequests,
            'total' => $reorderRequests->count()
        ]);
    }
    
    /**
     * Store a newly created reorder request
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'current_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:1',
            'suggested_quantity' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        $reorderRequest = ReorderRequest::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request created successfully',
            'data' => $reorderRequest
        ], 201);
    }
    
    /**
     * Display the specified reorder request
     */
    public function show(string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::with('product:product_id,name')->findOrFail($id);
        
        $reorderRequest->product_name = $reorderRequest->product ? $reorderRequest->product->name : null;
        unset($reorderRequest->product);
        
        return response()->json([
            'success' => true,
            'data' => $reorderRequest
        ]);
    }
    
    /**
     * Update the specified reorder request
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'sometimes|in:pending,approved,ordered,received,cancelled',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'suggested_quantity' => 'sometimes|integer|min:1',
            'estimated_cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expected_delivery' => 'nullable|date'
        ]);
        
        $reorderRequest->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request updated successfully',
            'data' => $reorderRequest
        ]);
    }
    
    /**
     * Approve a reorder request and update product inventory
     */
    public function approve(string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::with('product')->findOrFail($id);
        
        // Validate that the request can be approved
        if ($reorderRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending reorder requests can be approved'
            ], 400);
        }
        
        // Update the reorder request status
        $reorderRequest->update([
            'status' => 'approved',
            'approved_by' => 1, // In real app, use auth()->id()
            'approved_at' => now()
        ]);
        
        // Update product quantity (assuming immediate restocking upon approval)
        if ($reorderRequest->product && $reorderRequest->suggested_quantity > 0) {
            $product = $reorderRequest->product;
            $newQuantity = $product->quantity + $reorderRequest->suggested_quantity;
            
            $product->update([
                'quantity' => $newQuantity
            ]);
            
            // Auto-resolve related stock alerts if stock is now above reorder level
            if ($newQuantity > $product->reorder_level) {
                \App\Models\StockAlert::where('product_id', $product->product_id)
                    ->whereIn('status', ['active', 'acknowledged'])
                    ->update([
                        'status' => 'resolved',
                        'resolved_at' => now()
                    ]);
            }
            
            // Log the inventory update
            \Log::info("Product inventory updated", [
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'old_quantity' => $product->quantity - $reorderRequest->suggested_quantity,
                'new_quantity' => $newQuantity,
                'added_quantity' => $reorderRequest->suggested_quantity,
                'reorder_request_id' => $reorderRequest->id
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request approved and inventory updated successfully',
            'data' => [
                'reorder_request' => $reorderRequest,
                'updated_product_quantity' => $reorderRequest->product->quantity ?? null
            ]
        ]);
    }
    
    /**
     * Cancel a reorder request
     */
    public function cancel(string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::findOrFail($id);
        
        $reorderRequest->update([
            'status' => 'cancelled'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request cancelled successfully',
            'data' => $reorderRequest
        ]);
    }
    
    /**
     * Mark reorder request as ordered
     */
    public function markOrdered(Request $request, string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::findOrFail($id);
        
        $validated = $request->validate([
            'expected_delivery' => 'nullable|date|after:today',
            'notes' => 'nullable|string'
        ]);
        
        $reorderRequest->update([
            'status' => 'ordered',
            'expected_delivery' => $validated['expected_delivery'] ?? null,
            'notes' => $validated['notes'] ?? $reorderRequest->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request marked as ordered',
            'data' => $reorderRequest
        ]);
    }
    
    /**
     * Mark reorder request as received
     */
    public function markReceived(string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::findOrFail($id);
        
        $reorderRequest->update([
            'status' => 'received'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request marked as received',
            'data' => $reorderRequest
        ]);
    }
    
    /**
     * Get reorder request statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_requests' => ReorderRequest::count(),
            'pending_requests' => ReorderRequest::where('status', 'pending')->count(),
            'approved_requests' => ReorderRequest::where('status', 'approved')->count(),
            'ordered_requests' => ReorderRequest::where('status', 'ordered')->count(),
            'received_requests' => ReorderRequest::where('status', 'received')->count(),
            'cancelled_requests' => ReorderRequest::where('status', 'cancelled')->count(),
            'urgent_requests' => ReorderRequest::where('priority', 'urgent')->where('status', '!=', 'received')->count(),
            'high_priority_requests' => ReorderRequest::where('priority', 'high')->where('status', '!=', 'received')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Mark reorder request as received and update product inventory
     * This is an alternative to updating inventory on approval
     */
    public function receive(Request $request, string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::with('product')->findOrFail($id);
        
        // Validate that the request can be received
        if (!in_array($reorderRequest->status, ['approved', 'ordered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved or ordered reorder requests can be received'
            ], 400);
        }
        
        $validated = $request->validate([
            'actual_quantity' => 'required|integer|min:1',
            'actual_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
        
        // Update the reorder request
        $reorderRequest->update([
            'status' => 'received',
            'actual_quantity' => $validated['actual_quantity'],
            'actual_cost' => $validated['actual_cost'] ?? $reorderRequest->estimated_cost,
            'received_at' => now(),
            'notes' => $validated['notes'] ?? $reorderRequest->notes
        ]);
        
        // Update product quantity with actual received quantity
        if ($reorderRequest->product && $validated['actual_quantity'] > 0) {
            $product = $reorderRequest->product;
            $oldQuantity = $product->quantity;
            $newQuantity = $oldQuantity + $validated['actual_quantity'];
            
            $product->update([
                'quantity' => $newQuantity
            ]);
            
            // Log the inventory update
            \Log::info("Product inventory updated on receipt", [
                'product_id' => $product->product_id,
                'product_name' => $product->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'received_quantity' => $validated['actual_quantity'],
                'reorder_request_id' => $reorderRequest->id
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request marked as received and inventory updated',
            'data' => [
                'reorder_request' => $reorderRequest->fresh(),
                'updated_product_quantity' => $reorderRequest->product->fresh()->quantity ?? null
            ]
        ]);
    }
    
    /**
     * Remove the specified reorder request
     */
    public function destroy(string $id): JsonResponse
    {
        $reorderRequest = ReorderRequest::findOrFail($id);
        $reorderRequest->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Reorder request deleted successfully'
        ]);
    }
}
