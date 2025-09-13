<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminFactory extends Factory
{
    protected $model = Admin::class;

    public function definition(): array
    {
        return [
            'staffName' => 'System Admin',
            'staffEmail' => $this->faker->unique()->safeEmail(),
            'staffPhoneNumber' => $this->faker->phoneNumber(),
            'dateHired' => $this->faker->date(),
            'role' => 'admin',
            'password' => bcrypt('admin123'),
            'authorityLevel' => $this->faker->numberBetween(1,3),
        ];
    }
}
