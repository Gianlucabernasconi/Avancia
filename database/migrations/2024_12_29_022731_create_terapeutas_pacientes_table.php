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
        Schema::create('terapeutas_pacientes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_terapeuta')->unsigned();
            $table->integer('id_paciente')->unsigned();
            $table->timestamps();


            //FK
            $table->foreign('id_terapeuta')->references('id_terapeuta')->on('terapeutas')->onDelete('cascade');
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terapeutas_pacientes');
    }
};
