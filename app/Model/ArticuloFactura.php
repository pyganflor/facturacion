<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArticuloFactura extends Model
{
    protected $table= "articulo_factura";

    protected $primaryKey = "id_articulo_factura";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'descripcion',
        'cantidad',
        'codigo_p',
        'codigo_a',
        'precio_unitario',
        'descuento',
        'precio_total_sin_imp',
        'id_categoria_inventario',
        'id_articulo_categoria_inventario'
    ];

    public function impuestos(){
        return $this->hasMany('App\Model\ImpuestoArticuloFactura','id_articulo_factura');
    }

    public function categoria_inventario(){
        return $this->belongsTo('App\Model\CategoriaInventario','id_categoria_inventario');
    }
}
