<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            
            PacientesSeeder::class, 
            TerapeutaSeeder::class,
            ActividadSeeder::class, 
            GrupoSeeder::class,
            GrupoPacienteSeeder::class,
            InformeSeeder::class,
            TerapeutaPacienteSeeder::class,
            SesionSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
