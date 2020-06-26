<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table= "inventario";

    protected $primaryKey = "id_inventario";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'estado',
        'fecha_registro'
    ];

    public function categorias(){
        return $this->hasMany('App\Model\CategoriaInventario','id_inventario')->with('articulos');
    }


}
