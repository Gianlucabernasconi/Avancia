<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class TerapeutaFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => $this->faker->name(),
            'apellido' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'email_verified_at' => $this->faker->boolean(80) ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
            'role' => $this->faker->randomElement(['admin', 'terapeuta']),
        ];
    }
}
