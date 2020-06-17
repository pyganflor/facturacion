<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    protected $table= "tipo_pago";

    protected $primaryKey = "id_tipo_pago";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'fecha_registro'
    ];
}
