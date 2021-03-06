<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table= "factura";

    protected $primaryKey = "id_factura";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'secuencial',
        'clave_acceso',
        'fecha_doc',
        'fecha_aut',
        'entorno',
        'causa',
        'rehusar',
        'total',
        'carpeta',
        'fecha_venc',
        'comentario',
        'correos',
        'id_forma_pago',
        'id_sustento_tributario',
        'id_tipo_pago',
        'plazo',
        'und_tiempo',
        'estado'
    ];

    public function usuario(){
        return $this->belongsTo('App\Model\Usuario','id_usuario');
    }

    public function cliente(){
        return $this->belongsTo('App\Model\Cliente','id_cliente');
    }

    public function detalle(){
        return $this->belongsTo('App\Model\DetalleFactura','id_factura');
    }

    public function articulos(){
        return $this->hasMany('App\Model\ArticuloFactura','id_factura');
    }

    public function pagos(){
        return $this->hasMany('App\Model\FacturaPago','id_factura');
    }
}
