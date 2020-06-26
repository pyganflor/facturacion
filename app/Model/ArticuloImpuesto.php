<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ArticuloImpuesto extends Model
{

    protected $table= "articulo_impuesto";

    protected $primaryKey = "id_articulo_impuesto";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_articulo_categoria_inventario',
        'id_impuesto',
        'id_tipo_impuesto',
        'fecha_registro'
    ];

    public function impuesto(){
        return $this->belongsTo('App\Model\Impuesto','id_impuesto');
    }

    public function tipo_impuesto(){
        return $this->belongsTo('App\Model\TipoImpuesto','id_tipo_impuesto');
    }
}
