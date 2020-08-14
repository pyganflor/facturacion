<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImpuestosRetencion extends Model
{
    protected $table= "impuestos_retencion";

    protected $primaryKey = "id_impuestos_retencion";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo'
    ];

}
