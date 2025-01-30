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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->increments('id_paciente');
            $table->string('nombre');
            $table->string('apellido');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();
            $table->string('rut');
            $table->string('telefono')->nullable();
            $table->string('telefono_emergencia')->nullable();
            $table->string('direccion');
            $table->text('motivo_consulta');
            $table->text('diagnostico')->nullable();
            $table->enum('estado', ['activo', 'inactivo']);
            $table->date('fecha_ingreso');
            $table->date('fecha_egreso')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable()->useCurrent();
            $table->timestamps();
            
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
