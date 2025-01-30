<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoPaciente;

class GrupoPacienteSeeder extends Seeder
{
    public function run()
    {
        GrupoPaciente::factory()->count(20)->create();
    }
}
