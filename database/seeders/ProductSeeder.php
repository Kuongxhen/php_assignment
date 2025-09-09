<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Amoxicillin 500mg',
            'description' => 'Antibiotic used for infections',
            'category' => 'Medication',
            'sku' => 'MED-001',
            'price' => 12.50,
            'cost' => 8.00,
            'quantity' => 100,
            'unit' => 'capsules',
            'manufacturer' => 'Pfizer',
            'expiration_date' => '2026-12-31',
        ]);

        Product::create([
            'name' => 'Vitamin C 1000mg',
            'description' => 'Immune booster supplement',
            'category' => 'Supplement',
            'sku' => 'SUP-002',
            'price' => 8.90,
            'cost' => 5.00,
            'quantity' => 200,
            'unit' => 'tablets',
            'manufacturer' => 'Blackmores',
            'expiration_date' => '2027-06-30',
        ]);
    }
}
