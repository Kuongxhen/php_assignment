<?php

namespace Database\Factories;

use App\Models\Receptionist;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceptionistFactory extends Factory
{
    protected $model = Receptionist::class;

    public function definition(): array
    {
        return [
            'staffName' => $this->faker->name(),
            'staffEmail' => $this->faker->unique()->safeEmail(),
            'staffPhoneNumber' => $this->faker->phoneNumber(),
            'dateHired' => $this->faker->date(),
            'role' => 'receptionist',
            'password' => bcrypt('password'),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'shift' => $this->faker->randomElement(['morning', 'afternoon', 'evening', 'night']),
        ];
    }
}
