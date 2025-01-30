<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InformeFactory extends Factory
{
    public function definition()
    {
        return [
            'fecha_informe' => $this->faker->date(),
            'resumen' => $this->faker->paragraph(),
            'id_paciente' => $this->faker->numberBetween(1, 50),
            'id_terapeuta' => $this->faker->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
