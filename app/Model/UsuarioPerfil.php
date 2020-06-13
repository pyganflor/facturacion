<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsuarioPerfil extends Model
{
    protected $table= "usuario_perfil";

    protected $primaryKey = "id_usuario_perfil";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'razon_social',
        'nombre_comercial',
        'ruc',
        'direc_matriz',
        'direc_establecimiento',
        'contri_esp',
        'oblig_cont',
        'oblig_cont',
        'firma_elec',
        'firma_desde',
        'firma_hasta',
        'nombre_firma',
        'empresa_firma',
        'pass_firma_elec',
        'fecha_registro'
    ];
}
