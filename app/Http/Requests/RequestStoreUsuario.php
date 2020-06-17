<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Model\Usuario;
use Illuminate\Http\Request;

class RequestStoreUsuario extends FormRequest
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
            'nombre'=> 'required',
            'correo' => 'required',
            'modulos' => 'required|min:1',
            'idUsuario' => function($attribute,$value,$onFailure) use ($request){
                if(isset($value)){
                    $u = Usuario::find($value);
                    if(!isset($u))
                        $onFailure('El usuario no está registrado');

                }else{
                    if(!isset($request->contrasena))
                        $onFailure('La contraseña es obligatoria');
                }
            }
        ];
    }

    public function messages()
    {
        return [
            'nombre.required'=>'El usuario es obligatorio',
            'correo.required'=>'El correo es obligatorio',
            'modulos.min'=>'Al menos uno de los módulos débe ser habilitado',
            'modulos.required'=>'Los módulos son requeridos',
            'idUSuario.exists' => 'Este usuario no esta registrado'
        ];
    }
}
