<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categories = ['Medicine', 'Equipment', 'Supplies', 'Instruments', 'Consumables'];
        $units = ['pcs', 'box', 'bottle', 'pack', 'unit'];
        $manufacturers = ['MedCorp', 'HealthTech', 'PharmaCare', 'MediSupply', 'ClinicPro'];
        
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->text(200),
            'category' => $this->faker->randomElement($categories),
            'sku' => $this->faker->unique()->bothify('SKU-####-???'),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'cost' => $this->faker->randomFloat(2, 5, 300),
            'quantity' => $this->faker->numberBetween(0, 200),
            'reorder_level' => $this->faker->numberBetween(5, 50),
            'auto_reorder' => $this->faker->boolean(),
            'supplier' => $this->faker->company(),
            'image_path' => null,
            'unit' => $this->faker->randomElement($units),
            'manufacturer' => $this->faker->randomElement($manufacturers),
            'expiration_date' => $this->faker->optional()->dateTimeBetween('now', '+2 years'),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }
}
