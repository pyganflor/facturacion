<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsuarioFacturero extends Model
{
    protected $table= "usuario_facturero";

    protected $primaryKey = "id_usuario_facturero";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'numero',
        'fecha_registro'
    ];
}
