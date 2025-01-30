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
        Schema::create('informes', function (Blueprint $table) {
            $table->increments('id_informe');
            $table->date('fecha_informe');
            $table->text('resumen');
            $table->unsignedInteger('id_paciente'); 
            $table->unsignedInteger('id_terapeuta'); 
            $table->timestamps();
            

            //FK
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_terapeuta')->references('id_terapeuta')->on('terapeutas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informes');
    }
};
