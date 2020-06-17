<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificacion extends Model
{
    protected $table= "tipo_identificacion";

    protected $primaryKey = "id_tipo_identificacion";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'fecha_registro'
    ];
}
