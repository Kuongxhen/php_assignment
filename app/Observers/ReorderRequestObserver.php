<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ReorderRequest;
use Illuminate\Support\Facades\Log;

/**
 * Reorder Request Observer - Automatically generates reorder requests
 */
class ReorderRequestObserver implements StockObserverInterface
{
    public function update(Product $product): void
    {
        if ($this->shouldCreateReorderRequest($product)) {
            $this->createReorderRequest($product);
            Log::info("Automatic reorder request created for product: {$product->name} (ID: {$product->product_id})");
        }
    }

    /**
     * Check if we should create a reorder request
     */
    private function shouldCreateReorderRequest(Product $product): bool
    {
        // Don't create duplicate reorder requests
        $existingRequest = ReorderRequest::where('product_id', $product->product_id)
            ->where('status', 'pending')
            ->first();

        return !$existingRequest && 
               $product->quantity <= ($product->reorder_level ?? 10) &&
               $product->is_active;
    }

    /**
     * Create an automatic reorder request
     */
    private function createReorderRequest(Product $product): void
    {
        $suggestedQuantity = $this->calculateReorderQuantity($product);

        ReorderRequest::create([
            'product_id' => $product->product_id,
            'current_quantity' => $product->quantity,
            'reorder_level' => $product->reorder_level ?? 10,
            'suggested_quantity' => $suggestedQuantity,
            'estimated_cost' => $suggestedQuantity * ($product->cost_price ?? $product->cost ?? 0),
            'priority' => $this->calculatePriority($product),
            'status' => 'pending',
            'request_type' => 'automatic',
            'notes' => "Automatic reorder request generated due to low stock. Current: {$product->quantity}, Reorder level: {$product->reorder_level}",
            'requested_by' => null, // System generated
            'supplier' => $product->manufacturer
        ]);
    }

    /**
     * Calculate suggested reorder quantity
     */
    private function calculateReorderQuantity(Product $product): int
    {
        $reorderLevel = $product->reorder_level ?? 10;
        $currentQuantity = $product->quantity;
        
        // Suggest ordering enough to reach 3x the reorder level
        // This provides buffer stock to avoid frequent reorders
        $targetQuantity = $reorderLevel * 3;
        $suggestedQuantity = $targetQuantity - $currentQuantity;
        
        // Minimum order of 50 units or reorder level, whichever is higher
        return max($suggestedQuantity, max(50, $reorderLevel));
    }

    /**
     * Calculate request priority based on stock situation
     */
    private function calculatePriority(Product $product): string
    {
        $reorderLevel = $product->reorder_level ?? 10;
        $quantity = $product->quantity;

        if ($quantity <= 0) {
            return 'urgent'; // Out of stock
        } elseif ($quantity <= $reorderLevel * 0.3) {
            return 'high'; // Very low stock
        } elseif ($quantity <= $reorderLevel * 0.7) {
            return 'medium'; // Low stock
        } else {
            return 'low'; // Just below reorder level
        }
    }
}
