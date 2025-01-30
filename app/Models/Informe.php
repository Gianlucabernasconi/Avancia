<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Informe extends Model
{
    protected $table = 'informes';
    protected $primaryKey = 'id_informe';
    public $timestamps = true;
    use HasFactory;


    protected $fillable = [
        'fecha_informe',
        'resumen',
        'id_paciente',
        'id_terapeuta',


    ];

    protected $casts = [
        'fecha_informe' => 'date',
        'resumen' => 'string',
        'id_paciente' => 'integer',
        'id_terapeuta' => 'integer',
    ];


    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function terapeuta()
    {
        return $this->belongsTo(Terapeuta::class, 'id_terapeuta');
    }
}
