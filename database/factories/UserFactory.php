<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => $this->faker->randomElement(['patient', 'staff', 'doctor', 'admin']),
            'phone_number' => $this->faker->phoneNumber(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'date_of_birth' => $this->faker->dateTimeBetween('-70 years', '-18 years'),
            'employee_id' => null,
            'license_number' => null,
            'specialization' => null,
            'department' => null,
            'hire_date' => null,
            'patient_id' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'last_login' => $this->faker->optional()->dateTimeThisYear(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user with admin role
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'employee_id' => 'ADMIN' . $this->faker->unique()->numberBetween(1000, 9999),
            'department' => 'Administration',
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ]);
    }

    /**
     * Create a user with doctor role
     */
    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'doctor',
            'employee_id' => 'DOC' . $this->faker->unique()->numberBetween(1000, 9999),
            'license_number' => 'MD' . $this->faker->unique()->numberBetween(100000, 999999),
            'specialization' => $this->faker->randomElement([
                'General Medicine', 'Cardiology', 'Pediatrics', 'Neurology', 
                'Orthopedics', 'Dermatology', 'Psychiatry', 'Surgery'
            ]),
            'department' => $this->faker->randomElement([
                'Internal Medicine', 'Emergency', 'Surgery', 'Pediatrics', 'Cardiology'
            ]),
            'hire_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
        ]);
    }

    /**
     * Create a user with staff role
     */
    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'staff',
            'employee_id' => 'STAFF' . $this->faker->unique()->numberBetween(1000, 9999),
            'department' => $this->faker->randomElement([
                'Administration', 'Nursing', 'Laboratory', 'Pharmacy', 'Reception'
            ]),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ]);
    }

    /**
     * Create a user with patient role
     */
    public function patient(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'patient',
            'employee_id' => null,
            'license_number' => null,
            'specialization' => null,
            'department' => null,
            'hire_date' => null,
        ]);
    }
}
