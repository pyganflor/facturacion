<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DetalleRetencionCliente extends Model
{
    protected $table= "detalle_retencion_cliente";

    protected $primaryKey = "id_detalle_retencion_cliente";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_retencion_cliente',
        'codigo_tipo_impuesto',
        'codigo_retencion',
        'porcentaje_retenido',
        'valor_retenido',
        'cod_doc_sustento',
        'num_doc_sustento',
        'base_imponible',
        'fecha_emi_doc_sustento'
    ];

}
