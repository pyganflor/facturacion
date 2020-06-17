<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsuarioModulo extends Model
{
    protected $table= "usuario_modulo";

    protected $primaryKey = "id_usuario_modulo";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_modulo',
        'fecha_registro'
    ];

    public function modulo(){
        return $this->belongsTo('App\Model\Modulo','id_modulo');
    }
}
