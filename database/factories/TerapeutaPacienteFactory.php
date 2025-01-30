<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TerapeutaPacienteFactory extends Factory
{
    public function definition()
    {
        return [
            'id_terapeuta' => $this->faker->numberBetween(1, 5),
            'id_paciente' => $this->faker->numberBetween(1, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
