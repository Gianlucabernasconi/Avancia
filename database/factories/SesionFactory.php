<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SesionFactory extends Factory
{
    public function definition()
    {
        return [
            'id_paciente' => $this->faker->numberBetween(1, 50),
            'fecha_sesion' => $this->faker->date(),
            'duracion' => $this->faker->time(), 
            'nota' => $this->faker->optional()->text(200), 
            'created_at' => now(),
            'updated_at' => now(),
            'calificacion' => $this->faker->randomElement(['buena', 'mala', 'regular', 'muy buena', 'muy mala']),
            'emocion' => $this->faker->word(),
            'sintoma' => $this->faker->sentence(),
        ];
    }
}
