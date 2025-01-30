<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grupo_pacientes', function (Blueprint $table) {
            $table->id(); // Clave primaria automática
            $table->unsignedInteger('id_grupo');
            $table->unsignedInteger('id_paciente');
            $table->timestamps();

            //FK
            $table->foreign('id_grupo')->references('id_grupo')->on('grupos')->onDelete('cascade');
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');

          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_pacientes');
    }
};
