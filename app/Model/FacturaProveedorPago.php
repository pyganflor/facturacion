<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FacturaProveedorPago extends Model
{
    protected $table= "factura_proveedor_pago";

    protected $primaryKey = "id_factura_proveedor_pago";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_factura_proveedor',
        'id_tipo_pago',
        'id_forma_pago',
        'total',
        'und_tiempo',
        'cantidad'
    ];
}
