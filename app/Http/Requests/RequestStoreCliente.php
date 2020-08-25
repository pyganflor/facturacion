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
            'id_tipo_identificacion' => 'required',
            'correo' => 'required|email',
            'nombre' => 'required|max:300',
            'id_tipo_pago' => 'required',
            'codigo_pais' => 'required',
            'identificacion'=> ['required',function($attribute,$value,$onFailure) use ($request){

                if($request->id_tipo_identificacion == 5 && strlen($value) < 10 || $request->id_tipo_identificacion == 5 && strlen($value) > 20)
                    $onFailure('La identificación del cliente debe ser mínimo de 10 caracteres y máximo de 20');

                if(strlen($value) > 20)
                    $onFailure('La identificación del cliente debe ser menór a 20 caracteres');

                if($request->id_tipo_identificacion == 1 && strlen($value) != 13)
                    $onFailure('La identificación del cliente debe ser de 13 caracteres');

                if($request->id_tipo_identificacion == 4 && $value != "9999999999999")
                    $onFailure('La identificación de para consumido final deben ser 13 digitos de 9');

                $existeIdentificacion = Cliente::where([
                    ['id_usuario',Auth::id()],
                    ['identificacion',$value]
                ])->whereNotIn('id_cliente',[$request->id_cliente])->exists();

                if($existeIdentificacion)
                    $onFailure('La identificaión ya está en uso por otro cliente');

            }],
            'direccion' => 'required|max:300'
        ];
    }


    public function messages()
    {
        return [
            'id_tipo_identificacion.required'=>'Debe seleccionar el tipo de identificación del cliente',
            'identificacion.required'=>'Debe escribir la identificación del cliente',
            'correo.required' => 'Debe escribir un correo para el cliente, ya que a este se le hará el envío de la factura electrónica',
            'correo.email' => 'El correo debe ser un formato válido',
            'nombre.required' => 'Debe escribir el nombre o razón social del cliente',
            'nombre.max' => 'El nombre do razón social del cliente no debe ser de mas de 300 caracteres',
            'id_tipo_pago.required' => 'Debe seleccionar el tipo de pago que usará cliente',
            'codigo_pais.required' => 'Debe seleccionar el páis del cliente',
            'direccion.required' => 'Debe escribir la dirección del comprador',
            'direccion.max' => 'La dirección del comprador debe ser menor a 300 caracteres',
        ];
    }
}
