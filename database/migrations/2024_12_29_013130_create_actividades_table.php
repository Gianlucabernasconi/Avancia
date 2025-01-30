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
        Schema::create('actividades', function (Blueprint $table) {
            $table->increments('id_actividad');
            $table->text('descripcion');
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'cancelado']);
            $table->unsignedInteger('id_paciente');
            $table->timestamps();
            $table->string('nombre', 255);

            

            //FK
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
