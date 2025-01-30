<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Actividad;

class ActividadSeeder extends Seeder
{
    public function run()
    {
        Actividad::factory()->count(20)->create();
    }
}
