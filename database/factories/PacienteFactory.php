<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PacienteFactory extends Factory
{
    public function definition()
    {
        return [
            'nombre' => $this->faker->name(),
            'apellido'=> $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->date('Y-m-d'),
            'genero' => $this->faker->randomElement(['masculino', 'femenino', 'otro']),
            'rut' => $this->faker->unique()->numerify('########-#'), 
            'telefono' => $this->faker->numerify('#########'),
            'telefono_emergencia' => $this->faker->numerify('#########'),
            'direccion' => $this->faker->address(),
            'motivo_consulta' => $this->faker->sentence(),
            'diagnostico' => $this->faker->optional()->sentence(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
            'fecha_ingreso' => $this->faker->date('Y-m-d'),
            'fecha_egreso' => $this->faker->optional()->date('Y-m-d'),
            'fecha_actualizacion' => now(),
        ];
    }
}
