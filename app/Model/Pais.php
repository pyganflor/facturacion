<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table= "pais";

    protected $primaryKey = "codigo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fecha_registro'
    ];
}
