<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actividad extends Model
{

    protected $table = 'actividades';
    protected $primaryKey = 'id_actividad';
    public $timestamps = true;
    use HasFactory;




    protected $fillable = [
        'descripcion',
        'fecha_asignacion',
        'fecha_limite',
        'estado',
        'id_paciente',
        'nombre'
    ];

    protected $casts = [
        'descripcion' => 'string',
        'fecha_asignacion' => 'date',
        'fecha_limite' => 'date',
        'estado' => 'string',
        'nombre' => 'string',
    ];


    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

}


