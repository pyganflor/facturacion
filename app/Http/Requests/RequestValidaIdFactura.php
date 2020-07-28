<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Model\Factura;
use Illuminate\Support\Facades\Auth;

class RequestValidaIdFactura extends FormRequest
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
            'id_comprobante' => ['required',
                function($attribute,$value,$onFailure) use ($request){

                    switch($request->comprobante){
                        case 'factura':
                            $comprobante = Factura::class;
                            $id = 'id_factura';
                            break;
                    }

                    $match = $comprobante::where([
                        'id_usuario' => Auth::user()->id_usuario,
                        $id => $value
                    ])->exists();

                    if(!$match)
                        $onFailure('El comprobante enviado no pertenece al usuario logueado');
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_factura.required' => 'No se obtuvo el identificador de la factura',
            'id_factura.exists' => 'La factura no existe'
        ];
    }
}
