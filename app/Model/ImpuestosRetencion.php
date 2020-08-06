<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ImpuestosRetencion extends Model
{
    protected $table= "impuesto_retencion";

    protected $primaryKey = "id_impuesto_retencion";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo'
    ];

}
