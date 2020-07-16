<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table= "detalle_factura";

    protected $primaryKey = "id_detalle_factura";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
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
        'importe_total'
    ];


    public function impuestos(){
        return $this->hasMany('App\Model\ImpuestoDetalleFactura','id_detalle_factura');
    }
}
