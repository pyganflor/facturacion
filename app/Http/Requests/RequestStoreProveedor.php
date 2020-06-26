<?php

namespace App\Http\Requests;

use App\Model\Proveedor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RequestStoreProveedor extends FormRequest
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
            'identificacion' => ['required',function($attribute,$value,$onFailure) use ($request){

                $existsIdentifacion = Proveedor::where([['identificacion',$value],['id_usuario',Auth::user()->id_usuario]])->exists();
                if(isset($request->id_proveedor)){
                    $proveedor = Proveedor::find($request->id_proveedor);
                    if($value != $proveedor->identificacion)
                        if($existsIdentifacion)
                            $onFailure('La identificación ya se encuentra registrada con otro de sus proveedores');
                }else{
                    if($existsIdentifacion)
                        $onFailure('La identificación ya se encuentra registrada con otro de sus proveedores');
                }

            }],
            'nombre_comercial'=> 'required',
            'razon_social' =>'required',
            'tlf' =>'required|numeric',
            'correo' =>'required|email',
            'direccion' =>'required',
        ];
    }

    public function messages()
    {
        return [
            'identificacion.required' => 'La identificación es obligatoria',
            'nombre_comercial.required'=> 'el nombre comercial es obligatorio',
            'razon_social.required'=> 'La razón social es obligatoria',
            'tlf.required'=> 'El teléfono es obligatorio',
            'tlf.numeric'=> 'El teléfono debe ser sólo números',
            'correo.required'=> 'El correo es obligatorio',
            'correo.email'=> 'El correo debe ser un formato válido',
            'direccion.required'=> 'La dirección es obligatoria',
        ];
    }
}
