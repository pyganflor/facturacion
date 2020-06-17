<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{
    protected $table= "tipo_comprobante";

    protected $primaryKey = "id_tipo_comprobante";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'fecha_registro'
    ];
}
