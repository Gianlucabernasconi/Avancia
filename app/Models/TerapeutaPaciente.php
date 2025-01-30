<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerapeutaPaciente extends Model
{
    use HasFactory;

    protected $table = 'terapeutas_pacientes'; 
    protected $primaryKey = 'id'; 
    public $timestamps = true; 
    protected $fillable = ['id_terapeuta', 'id_paciente']; 
}
