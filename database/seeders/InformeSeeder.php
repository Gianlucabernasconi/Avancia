<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Informe;

class InformeSeeder extends Seeder
{
    public function run()
    {
        Informe::factory()->count(10)->create();
    }
}
