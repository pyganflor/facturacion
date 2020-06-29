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
        'contrasena',
        'imagen',
        'correo',
        'tlf',
        'estado',
        'remember_token',
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

    public function perfil(){
        return $this->hasOne('App\Model\UsuarioPerfil','id_usuario');
    }

    public function modulos(){
        return $this->hasMany('App\Model\UsuarioModulo','id_usuario')->with('modulo');
    }

    public function inventario(){
        return $this->hasOne('App\Model\Inventario','id_usuario');
    }

    public function ptoEmision(){
        return $this->hasMany('App\Model\UsuarioPtoEmision','id_usuario');
    }

    public function factureros(){
        return $this->hasMany('App\Model\UsuarioFacturero','id_usuario');
    }
}

