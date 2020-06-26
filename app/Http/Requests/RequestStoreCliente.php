<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Cliente;

class RequestStoreCliente extends FormRequest
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
            'data.id_tipo_identificacion' => 'required',
            'data.correo' => 'required|email',
            'data.nombre' => 'required|max:300',
            'data.id_tipo_pago' => 'required',
            'data.codigo_pais' => 'required',
            'data.identificacion'=> ['required',function($attribute,$value,$onFailure) use ($request){

                if($request->data['id_tipo_identificacion'] == 5 && strlen($value) < 10 || $request->data['id_tipo_identificacion'] == 5 && strlen($value) > 20)
                    $onFailure('La identificación del cliente debe ser mínimo de 10 caracteres y máximo de 20');

                if(strlen($value) > 20)
                    $onFailure('La identificación del cliente debe ser menór a 20 caracteres');

                if($request->data['id_tipo_identificacion'] == 1 && strlen($value) != 13)
                    $onFailure('La identificación del cliente debe ser de 13 caracteres');

                if($request->data['id_tipo_identificacion'] == 4 && $value != "9999999999999")
                    $onFailure('La identificación de para consumido final deben ser 13 digitos de 9');

                $existeIdentificacion = Cliente::where([
                    ['id_usuario',Auth::id()],
                    ['identificacion',$value]
                ])->whereNotIn('id_cliente',[$request->data['id_cliente']])->exists();

                if($existeIdentificacion)
                    $onFailure('La identificaión ya está en uso por otro cliente');


            }],
            'data.direccion' => 'required|max:300'
        ];
    }


    public function messages()
    {
        return [
            'data.id_tipo_identificacion.required'=>'Debe seleccionar el tipo de identificación del cliente',
            'data.identificacion.required'=>'Debe escribir la identificación del cliente',
            'data.correo.required' => 'Debe escribir un correo para el cliente, ya que a este se le hará el envío de la factura electrónica',
            'data.correo.email' => 'El correo debe ser un formato válido',
            'data.nombre.required' => 'Debe escribir el nombre o razón social del cliente',
            'data.nombre.max' => 'El nombre do razón social del cliente no debe ser de mas de 300 caracteres',
            'data.id_tipo_pago.required' => 'Debe seleccionar el tipo de pago que usará cliente',
            'codigo_pais.required' => 'Debe seleccionar el páis del cliente',
            'data.direccion.required' => 'Debe escribir la dirección del comprador',
            'data.direccion.max' => 'La dirección del comprador debe ser menor a 300 caracteres',
        ];
    }
}
