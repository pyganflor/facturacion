<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TipoImpuesto extends Model
{
    protected $table= "tipo_impuesto";

    protected $primaryKey = "id_tipo_impuesto";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_impuesto',
        'codigo',
        'tarifa',
        'descripcion',
        'tipo_tarifa',
        'fecha_registro'
    ];
}
