<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockAlert;
use Illuminate\Support\Facades\Log;

/**
 * Stock Alert Observer - Creates low stock alerts
 */
class StockAlertObserver implements StockObserverInterface
{
    public function update(Product $product): void
    {
        if ($this->shouldCreateAlert($product)) {
            $this->createStockAlert($product);
            Log::info("Low stock alert created for product: {$product->name} (ID: {$product->product_id})");
        }
    }

    /**
     * Check if we should create an alert for this product
     */
    private function shouldCreateAlert(Product $product): bool
    {
        // Don't create duplicate alerts for the same product on the same day
        $existingAlert = StockAlert::where('product_id', $product->product_id)
            ->where('alert_type', 'low_stock')
            ->where('status', 'active')
            ->whereDate('created_at', today())
            ->first();

        return !$existingAlert && $product->quantity <= ($product->reorder_level ?? 10);
    }

    /**
     * Create a stock alert record
     */
    private function createStockAlert(Product $product): void
    {
        StockAlert::create([
            'product_id' => $product->product_id,
            'alert_type' => 'low_stock',
            'message' => "Low stock alert: {$product->name} has only {$product->quantity} units remaining (Reorder level: {$product->reorder_level})",
            'current_quantity' => $product->quantity,
            'reorder_level' => $product->reorder_level ?? 10,
            'status' => 'active',
            'severity' => $this->calculateSeverity($product)
        ]);
    }

    /**
     * Calculate alert severity based on how low the stock is
     */
    private function calculateSeverity(Product $product): string
    {
        $reorderLevel = $product->reorder_level ?? 10;
        $quantity = $product->quantity;

        if ($quantity <= 0) {
            return 'critical'; // Out of stock
        } elseif ($quantity <= $reorderLevel * 0.5) {
            return 'high'; // Half of reorder level
        } else {
            return 'medium'; // Below reorder level but not critical
        }
    }
}
