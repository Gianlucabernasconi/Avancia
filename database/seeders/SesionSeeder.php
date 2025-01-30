<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sesion;

class SesionSeeder extends Seeder
{
    public function run()
    {
        Sesion::factory()->count(20)->create(); 
    }
}
