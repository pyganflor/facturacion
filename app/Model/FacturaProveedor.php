<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FacturaProveedor extends Model
{
    protected $table= "factura_proveedor";

    protected $primaryKey = "id_factura_proveedor";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'id_usuario',
        'secuencial',
        'fecha_doc',
        'fecha_aut',
        'entorno',
        'clave_acceso',
        'total',
        'fecha_venc',
        'comentario',
        'correos',
        'id_sustento_tributario',
        'electronica',
        'estado',
        'fecha_registro'
    ];

    public function usuario(){
        return $this->belongsTo('App\Model\Usuario','id_usuario');
    }

    public function proveedor(){
        return $this->belongsTo('App\Model\Proveedor','id_proveedor');
    }

    public function detalle(){
        return $this->belongsTo('App\Model\DetalleFacturaProveedor','id_factura_proveedor');
    }

    public function articulos(){
        return $this->hasMany('App\Model\ArticuloFacturaProveedor','id_factura_proveedor');
    }

    public function pagos(){
        return $this->hasMany('App\Model\FacturaProveedorPago','id_factura_proveedor');
    }
}
