<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'ic_number' => $this->faker->unique()->numerify('############'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'emergency_contact_relationship' => $this->faker->randomElement(['spouse', 'parent', 'sibling', 'child', 'friend']),
            'medical_history' => $this->faker->optional()->text(200),
            'allergies' => $this->faker->optional()->randomElement(['None', 'Penicillin', 'Nuts', 'Shellfish', 'Latex']),
            'current_medications' => $this->faker->optional()->text(100),
            'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'chronic_conditions' => $this->faker->optional()->randomElement(['None', 'Diabetes', 'Hypertension', 'Asthma', 'Heart Disease']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'deceased']),
            'last_visit' => $this->faker->optional()->dateTimeThisYear(),
            'notes' => $this->faker->optional()->text(150),
        ];
    }
}
