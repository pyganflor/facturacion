<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RetencionCliente extends Model
{
    protected $table= "retencion_cliente";

    protected $primaryKey = "id_retencion_cliente";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_factura',
        'fecha_doc',
        'n_factura',
        'iva',
        'renta',
        'total',
        'estado',
        'comentario',
        'fecha_registro'
    ];
}
