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
        'razon_social',
        'ruc',
        'clave_acceso',
        'dir_matriz',
        'fecha_emision',
        'dir_establecimiento',
        'contribuyente_especial',
        'obligado_cont',
        'tipo_ident_suj_retendio',
        'razon_social_suj_retendio',
        'periodo_fiscal',
        'fecha_contabilidad',
        'electronica',
        'estado'
    ];
}
