<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'staffName' => $this->faker->name(),
            'staffEmail' => $this->faker->unique()->safeEmail(),
            'staffPhoneNumber' => $this->faker->phoneNumber(),
            'dateHired' => $this->faker->date(),
            'role' => 'doctor',
            'password' => bcrypt('password'),
            'specialization' => $this->faker->randomElement(['Cardiology','Pediatrics','Neurology','Orthopedics','Dermatology']),
        ];
    }
}
