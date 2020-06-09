<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use App\Model\Usuario;

class SaveAccesos extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'usuario' => function($attribute,$value,$onFailure) use ($request){
                if(strlen($value)<1)
                    $onFailure('El '. $attribute. ' es obligatorio');

                $usuario = Usuario::find($request->idUsuario);

                if($usuario->nombre != $value){
                    if(Usuario::where('nombre',$request->usuario)->exists())
                        $onFailure('El '. $attribute. ' de usuario ya está en uso, por favor seleccione otro');
                }
            },
            'pass'=>function($attribute,$value,$onFailure) use ($request){
                if(strlen($value)>0){
                    $usuario = Usuario::find($request->idUsuario);

                    if (Hash::check($request->actualPass,$usuario->contrasena)){




                    }else{
                        $onFailure('La contraseña actual no coincide');
                    }
                }
            }
        ];
    }
}
