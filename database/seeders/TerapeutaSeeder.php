<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Terapeuta;

class TerapeutaSeeder extends Seeder
{
    public function run()
    {
        Terapeuta::factory()->count(5)->create();
    }
}
