<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table= "rol";

    protected $primaryKey = "id_rol";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_registro'
    ];
}
