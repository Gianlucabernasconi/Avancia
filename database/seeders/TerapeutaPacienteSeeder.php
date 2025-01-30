<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TerapeutaPaciente;

class TerapeutaPacienteSeeder extends Seeder
{
    public function run()
    {
        TerapeutaPaciente::factory()->count(20)->create();
    }
}
