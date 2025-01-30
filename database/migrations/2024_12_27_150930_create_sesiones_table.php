<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    
     
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {

        $table->increments('id_sesion');
        $table->integer('id_paciente')->unsigned();
        $table->date('fecha_sesion');
        $table->enum('calificacion', ['buena', 'mala', 'regular', 'muy buena', 'muy mala']);
        $table->time('duracion')->nullable();
        $table->text('nota')->nullable();
        $table->text('emocion');
        $table->text('sintoma');
        $table->timestamps();

              
        // FK
        $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};
