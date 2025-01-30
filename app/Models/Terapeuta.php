<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Terapeuta extends Authenticatable
{
    protected $table = 'terapeutas';
    protected $primaryKey = 'id_terapeuta';
    public $timestamps = true;
    use HasFactory;


    protected $fillable = [
        'nombre',
        'email',
        'password',
        'role',
        'apellido'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    

    ];


    protected $casts = [
        'nombre' => 'string',
        'apellido' => 'string',
        'email' => 'string',
        'password' => 'string',
    ];

    public function getAuthPassword() //De esta manera laravel sabe que el campo contraseña debe usarse para autenticacion.
    {
        return $this->password;
    }

    public function hasRole($roleName)
    {
        return $this->role === $roleName;
    }

}
