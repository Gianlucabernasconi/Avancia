<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    protected $table = 'grupos';
    protected $primaryKey = 'id_grupo';
    public $timestamps = true;
    use HasFactory;
    
    
    
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'id_terapeuta',
        
    ];

    protected $casts = [
        'nombre' => 'string',
        'descripcion' => 'string',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'estado' => 'boolean',
        'id_terapeuta' => 'integer',
    ];


    public function terapeutas()
    {
        return $this->belongsToMany(Terapeuta::class, 'grupo_terapeutas', 'id_grupo', 'id_terapeuta');
    }

    public function pacientes()
    {
        return $this->belongsToMany(Paciente::class, 'grupo_pacientes', 'id_grupo', 'id_paciente');
    }

}
