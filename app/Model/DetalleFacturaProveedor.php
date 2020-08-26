<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DetalleFacturaProveedor extends Model
{
    protected $table= "detalle_factura_proveedor";

    protected $primaryKey = "id_detalle_factura_proveedor";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_factura_proveedor',
        'razon_social_emisor',
        'direc_matriz_emisor',
        'direc_establecimiento_emisor',
        'obligado_contabilidad',
        'razon_social_comprador',
        'tipo_ident_comprador',
        'ident_comprador',
        'total_sin_imp',
        'total_desc',
        'propina',
        'importe_total',
        'fecha_registro'
    ];


    public function impuestos(){
        return $this->hasMany('App\Model\ImpuestoDetalleFacturaProveedor','id_detalle_factura_proveedor');
    }
}
