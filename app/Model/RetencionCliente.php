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
        'secuencial',
        'clave_acceso',
        'dir_matriz',
        'fecha_emision',
        'dir_establecimiento',
        'contribuyente_especial',
        'obligado_cont',
        'tipo_ident_suj_retenido',
        'ident_suj_retenido',
        'razon_social_suj_retenido',
        'periodo_fiscal',
        'fecha_contabilidad',
        'electronica',
        'comentario',
        'estado'
    ];

    public function detalles(){
        return $this->hasMany('App\Model\DetalleRetencionCliente','id_retencion_cliente');
    }

    public function factura(){
        return $this->belongsTo('App\Model\Factura','id_factura');
    }

}
