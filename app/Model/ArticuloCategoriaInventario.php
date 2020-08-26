<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArticuloCategoriaInventario extends Model
{
    protected $table= "articulo_categoria_inventario";

    protected $primaryKey = "id_articulo_categoria_inventario";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_categoria_inventario',
        'articulo',
        'neto',
        'stock',
        'codigo_p',
        'codigo_a',
        'estado',
        'und',
        'venta',
        'fecha_registro'
    ];

    public function impuestos(){
        return $this->hasMany('App\Model\ArticuloImpuesto','id_articulo_categoria_inventario')
                        ->with('impuesto')->with('tipo_impuesto');
    }

    public function categoria(){
        return $this->belongsTo('App\Model\CategoriaInventario','id_categoria_inventario');
    }
}
