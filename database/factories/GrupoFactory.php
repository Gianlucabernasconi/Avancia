<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    public function definition()
    {
        return [
            'descripcion' => $this->faker->sentence(),
            'fecha_inicio' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
            'nombre' => $this->faker->word(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
