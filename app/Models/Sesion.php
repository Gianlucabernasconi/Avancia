<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sesion extends Model
{
    protected $table = 'sesiones';
    protected $primaryKey = 'id_sesion';
    public $timestamps = true;
    use HasFactory;




    protected $fillable = [
        'id_paciente',
        'id_categoria',
        'id_emocion',
        'fecha_sesion',
        'duracion',
        'nota',
        'calificacion',
        'sintoma',
        'emocion',
        
    ];


    protected $casts = [
        'id_paciente' => 'integer',
        'id_categoria' => 'integer',
        'id_emocion' => 'integer',
        'fecha_sesion' => 'date',
        'duracion' => 'string',
        'nota' => 'string',
        'calificacion' => 'string',
        'sintoma' => 'string',
        'emocion' => 'string',
    ];


    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }}

    
