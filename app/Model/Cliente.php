<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table= "cliente";

    protected $primaryKey = "id_cliente";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_tipo_identificacion',
        'nombre',
        'identificacion',
        'tlf',
        'correo',
        'direccion',
        'plazo_pago',
        'id_tipo_pago',
        'ut_plazo_pago',
        'codigo_pais',
        'estado',
        'fecha_registro'
    ];

    public function tipo_identificacion(){
        return $this->belongsTo('App\Model\TipoIdentificacion','id_tipo_identificacion');
    }



}
