<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DetalleImpuestosRetencion extends Model
{
    protected $table= "detalle_impuesto_retencion";

    protected $primaryKey = "id_detalle_impuesto_retencion";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_impuesto_retencion',
        'nombre',
        'porcentaje',
        'campo_formulario',
        'codigo'
    ];
}
