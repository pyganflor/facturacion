<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table= "usuario";

    protected $primaryKey = "id_usuario";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'imagen',
        'fecha_registro'
    ];

    protected $hidden = [
        'contrasena','remember_token'
    ];

    protected $guarded=[
        'id_usuario'
    ];

    public function getAuthPassword() {
        return $this->contrasena;
    }


    public function roles(){

        return $this->hasMany('App\Model\UsuarioRol','id_usuario');

    }
}
