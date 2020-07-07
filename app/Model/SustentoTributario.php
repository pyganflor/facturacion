<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SustentoTributario extends Model
{
    protected $table= "sustento_tributario";

    protected $primaryKey = "id_sustento_tributario";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'fecha_registro'
    ];
}
