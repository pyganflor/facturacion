<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{

    protected $table= "proveedor";

    protected $primaryKey = "id_proveedor";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'identificacion',
        'nombre_comercial',
        'razon_social',
        'tlf',
        'correo',
        'direccion',
        'banco',
        'tipo_cta',
        'numero_cta',
        'estado',
        'fecha_registro'
    ];

}
