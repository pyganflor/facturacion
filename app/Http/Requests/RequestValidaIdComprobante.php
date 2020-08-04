<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Model\Factura;
use Illuminate\Support\Facades\Auth;

class RequestValidaIdComprobante extends FormRequest
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
                        $onFailure('la '.$request->comprobante.' enviada no pertenece al usuario logueado');
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'id_comprobante.required' => 'No se obtuvo el identificador del comprobante',
            'id_comprobante.exists' => 'El comprobante no existe'
        ];
    }
}
