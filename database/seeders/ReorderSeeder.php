<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ReorderRequest;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReorderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products to create reorder requests for
        $products = Product::all();
        
        if ($products->count() === 0) {
            $this->command->warn('No products found. Please run ProductSeeder first.');
            return;
        }

        // Get admin users for requests and approvals
        $admins = Admin::all();

        foreach ($products as $product) {
            // Generate 1-2 reorder requests per product
            $requestCount = rand(1, 2);
            
            for ($i = 0; $i < $requestCount; $i++) {
                // Determine quantities and priority
                $currentQuantity = rand(0, 50);
                $reorderLevel = rand(20, 40);
                $suggestedQuantity = max($reorderLevel * 2, 50);
                
                $priority = 'medium';
                if ($currentQuantity === 0) {
                    $priority = 'urgent';
                } elseif ($currentQuantity <= ($reorderLevel * 0.3)) {
                    $priority = 'high';
                } elseif ($currentQuantity <= ($reorderLevel * 0.7)) {
                    $priority = 'medium';
                } else {
                    $priority = 'low';
                }

                // Determine status and timing
                $statuses = ['pending', 'approved', 'ordered', 'received', 'cancelled'];
                $status = $statuses[array_rand($statuses)];
                
                $estimatedCost = $suggestedQuantity * $product->cost * rand(95, 105) / 100; // +/- 5%
                $suppliers = ['MediSupply Co.', 'HealthCare Distributors', 'Medical Plus Inc.', 'PharmaSource Ltd.', 'Global Med Supply'];
                $supplier = $suppliers[array_rand($suppliers)];
                
                $requestedBy = $admins->count() > 0 ? $admins->random()->id : null;
                $approvedBy = null;
                $approvedAt = null;
                $expectedDelivery = null;
                
                $notes = $this->generateNotes($priority, $status);

                // Set approval and delivery dates based on status
                if (in_array($status, ['approved', 'ordered', 'received'])) {
                    $approvedBy = $admins->count() > 0 ? $admins->random()->id : null;
                    $approvedAt = Carbon::now()->subDays(rand(1, 10));
                    $expectedDelivery = $approvedAt->copy()->addDays(rand(3, 14));
                }

                ReorderRequest::create([
                    'product_id' => $product->product_id,
                    'current_quantity' => $currentQuantity,
                    'reorder_level' => $reorderLevel,
                    'suggested_quantity' => $suggestedQuantity,
                    'priority' => $priority,
                    'status' => $status,
                    'estimated_cost' => $estimatedCost,
                    'supplier' => $supplier,
                    'notes' => $notes,
                    'requested_by' => $requestedBy,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                    'expected_delivery' => $expectedDelivery,
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Reorder requests seeded successfully!');
    }

    /**
     * Generate appropriate notes based on priority and status
     */
    private function generateNotes($priority, $status): string
    {
        $notes = [
            'urgent' => [
                'Critical stock shortage - immediate reorder required',
                'Emergency procurement needed',
                'Patient care impact if not restocked soon'
            ],
            'high' => [
                'Stock levels critically low',
                'Expedited shipping recommended',
                'Monitor usage closely'
            ],
            'medium' => [
                'Regular reorder cycle',
                'Standard procurement process',
                'Maintain adequate stock levels'
            ],
            'low' => [
                'Preventive reorder',
                'Bulk purchase opportunity',
                'Stock optimization'
            ]
        ];

        $statusNotes = [
            'pending' => 'Awaiting management approval',
            'approved' => 'Approved for procurement',
            'ordered' => 'Purchase order sent to supplier',
            'received' => 'Stock received and verified',
            'cancelled' => 'Request cancelled due to budget constraints'
        ];

        $priorityNote = $notes[$priority][array_rand($notes[$priority])];
        $statusNote = $statusNotes[$status];
        
        return $priorityNote . '. ' . $statusNote . '.';
    }
}