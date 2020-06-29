<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsuarioPtoEmision extends Model
{
    protected $table= "usuario_pto_emision";

    protected $primaryKey = "id_usuario_pto_emision";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'numero',
        'fecha_registro'
    ];
}
