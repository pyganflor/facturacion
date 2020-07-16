<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImpuestoArticuloFactura extends Model
{
    protected $table= "impuesto_articulo_factura";

    protected $primaryKey = "id_impuesto_articulo_factura";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_articulo_factura',
        'codigo_impuesto',
        'codigo_porcentaje',
        'base_imponible',
        'valor'
    ];
}
