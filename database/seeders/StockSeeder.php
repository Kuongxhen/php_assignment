<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products to create stock alerts for
        $products = Product::all();
        
        if ($products->count() === 0) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // Get admin users for acknowledgments
        $admins = Admin::all();

        foreach ($products as $product) {
            // Generate 1-2 stock alerts per product
            $alertCount = rand(1, 2);
            
            for ($i = 0; $i < $alertCount; $i++) {
                // Determine alert type and quantities
                $currentQuantity = rand(0, 100);
                $reorderLevel = rand(20, 50);
                
                $alertType = 'low_stock';
                $severity = 'medium';
                
                if ($currentQuantity === 0) {
                    $alertType = 'out_of_stock';
                    $severity = 'critical';
                } elseif ($currentQuantity <= ($reorderLevel * 0.3)) {
                    $alertType = 'low_stock';
                    $severity = 'high';
                } elseif ($currentQuantity <= ($reorderLevel * 0.5)) {
                    $alertType = 'low_stock';
                    $severity = 'medium';
                } else {
                    $alertType = 'low_stock';
                    $severity = 'low';
                }

                // Add expired alerts for some products (15% chance)
                if (rand(1, 100) <= 15) {
                    $alertType = 'expired';
                    $severity = 'high';
                }

                // Generate appropriate message
                $message = $this->generateAlertMessage($alertType, $product->name, $currentQuantity, $reorderLevel);

                // Determine status and acknowledgment
                $status = 'active';
                $acknowledgedBy = null;
                $acknowledgedAt = null;
                $resolvedAt = null;

                // 40% chance of being acknowledged
                if (rand(1, 100) <= 40 && $admins->count() > 0) {
                    $status = 'acknowledged';
                    $acknowledgedBy = $admins->random()->id;
                    $acknowledgedAt = Carbon::now()->subDays(rand(1, 7));
                    
                    // 30% chance of acknowledged alerts being resolved
                    if (rand(1, 100) <= 30) {
                        $status = 'resolved';
                        $resolvedAt = $acknowledgedAt->copy()->addDays(rand(1, 3));
                    }
                }

                StockAlert::create([
                    'product_id' => $product->product_id,
                    'alert_type' => $alertType,
                    'message' => $message,
                    'current_quantity' => $currentQuantity,
                    'reorder_level' => $reorderLevel,
                    'status' => $status,
                    'severity' => $severity,
                    'acknowledged_by' => $acknowledgedBy,
                    'acknowledged_at' => $acknowledgedAt,
                    'resolved_at' => $resolvedAt,
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Stock alerts seeded successfully!');
    }

    /**
     * Generate appropriate alert message
     */
    private function generateAlertMessage($alertType, $productName, $currentQuantity, $reorderLevel): string
    {
        switch ($alertType) {
            case 'out_of_stock':
                return "URGENT: {$productName} is completely out of stock! Current quantity: 0. Please reorder immediately.";
            
            case 'low_stock':
                return "Low stock alert for {$productName}. Current quantity: {$currentQuantity}, Reorder level: {$reorderLevel}. Consider restocking soon.";
            
            case 'expired':
                return "Product expiration alert for {$productName}. Some units may be approaching or past expiration date. Please check inventory.";
            
            default:
                return "Stock alert for {$productName}. Current quantity: {$currentQuantity}.";
        }
    }
}