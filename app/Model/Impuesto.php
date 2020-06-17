<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    protected $table= "impuesto";

    protected $primaryKey = "id_impuesto";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo',
        'fecha_registro'
    ];

    public function tipo_impuesto(){
        return $this->hasMany('App\Model\TipoImpuesto','id_impuesto');
    }
}
