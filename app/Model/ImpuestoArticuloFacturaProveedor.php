<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImpuestoArticuloFacturaProveedor extends Model
{
    protected $table= "impuesto_articulo_factura_proveedor";

    protected $primaryKey = "id_impuesto_articulo_factura_proveedor";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_articulo_factura_proveedor',
        'codigo_impuesto',
        'codigo_porcentaje',
        'base_imponible',
        'valor'
    ];
}
