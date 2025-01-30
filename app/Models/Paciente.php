<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Paciente extends Model
{

    protected $table = 'pacientes';
    protected $primaryKey = 'id_paciente';
    public $timestamps = true;
    use HasFactory;




    protected $fillable = [
        'nombre',
        'genero',
        'telefono',
        'telefono_emergencia',
        'direccion',
        'email',
        'id_usuario',
        'motivo_consulta',
        'estado',
        'fecha_ingreso',
        'fecha_egreso',
        'rut',
        'apellido',

    ];

   protected $casts = [
        'nombre' => 'string',
        'genero' => 'string',
        'telefono' => 'string',
        'telefono_emergencia' => 'string' ,
        'direccion' => 'string',
        'email' => 'string',
        'motivo_consulta' => 'string',
        'estado' => 'string',
        'fecha_ingreso'  => 'date',
        'fecha_egreso'  => 'date',
        'apellido' => 'string',

   ];


    public function terapeutas()
    {
        return $this->belongsToMany(Terapeuta::class, 'terapeutas_pacientes', 'id_paciente', 'id_terapeuta');
    }

    
    public function sesiones()
    {
        return $this->hasMany(Sesion::class, 'id_paciente', 'id_paciente');
    }

    
    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_paciente', 'id_paciente');
    }

   
    public function informes()
    {
        return $this->hasMany(Informe::class, 'id_paciente', 'id_paciente');
    }

   
    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupos_pacientes', 'id_paciente', 'id_grupo');
    }
}



