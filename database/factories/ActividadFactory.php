<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    public function definition()
    {
        return [
            'descripcion' => $this->faker->sentence(),
            'fecha_asignacion' => $this->faker->date(),
            'fecha_limite' => $this->faker->optional()->date(),
            'estado' => $this->faker->randomElement(['pendiente', 'completado', 'cancelado']),
            'id_paciente' => $this->faker->numberBetween(1, 50),
            'nombre' => $this->faker->word(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
