<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table= "modulo";

    protected $primaryKey = "id_modulo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro'
    ];
}
