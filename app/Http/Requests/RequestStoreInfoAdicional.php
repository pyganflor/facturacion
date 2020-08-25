<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RequestStoreInfoAdicional extends FormRequest
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
            'entorno'=> 'required',
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'factureros' => 'required|Array|min:1',
            'ptoEmision' => 'required|Array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'id_usuario.required' => 'No se obtuvo el identificador del usuario',
            'id_usuario.exists' => 'El usuario no se encuentra registrado',
            'entorno.required' => 'No se obtuvo el entorno en el que se van a hacer las autorizaciones en el SRI',
            'factureros.required' => 'No se obtuvieron los factureros',
            'factureros.min' => 'Debe ingresar almenos un facturero',
            'ptoEmision.required' => 'No se obtuvieron los puntos de emisión',
            'ptoEmision.min' => 'Debe ingresar almenos un punto de emisión'
        ];
    }
}
