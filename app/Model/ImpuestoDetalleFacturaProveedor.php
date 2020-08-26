<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImpuestoDetalleFacturaProveedor extends Model
{
    protected $table= "impuesto_detalle_factura_proveedor";

    protected $primaryKey = "id_impuesto_detalle_factura_proveedor";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_factura_proveedor',
        'codigo_impuesto',
        'codigo_porcentaje',
        'base_imponible',
        'valor'
    ];
}
