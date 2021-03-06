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

    protected function rolViewActions(){
        return $this->hasMany('App\Model\RolViewAction','id_rol');
    }
}
