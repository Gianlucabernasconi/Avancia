<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoPaciente extends Model
{
    use HasFactory;

    protected $table = 'grupo_pacientes'; 
    protected $primaryKey = 'id';
    public $timestamps = true; 
    protected $fillable = ['id_grupo', 'id_paciente']; 
}
