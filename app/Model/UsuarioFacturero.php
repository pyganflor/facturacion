<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsuarioFacturero extends Model
{
    protected $table= "usuario_facturero";

    protected $primaryKey = "id_usuario_facturero";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'numero',
        'n_factura',
        'n_guia_remision',
        'n_retencion',
        'n_nota_debito',
        'n_nota_credito',
        'fecha_registro'
    ];

}
