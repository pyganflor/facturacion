<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FacturaPago extends Model
{
    protected $table= "factura_pago";

    protected $primaryKey = "id_factura_pago";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_factura',
        'id_tipo_pago',
        'id_forma_pago',
        'total',
        'und_tiempo',
        'cantidad'
    ];
}
