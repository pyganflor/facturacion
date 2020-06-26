<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CategoriaInventario extends Model
{
    protected $table= "categoria_inventario";

    protected $primaryKey = "id_categoria_inventario";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_inventario',
        'categoria',
        'estado',
        'fecha_registro'
    ];

    public function articulos(){
        return $this->hasMany('App\Model\ArticuloCategoriaInventario','id_categoria_inventario')->with('impuestos');
    }
}
