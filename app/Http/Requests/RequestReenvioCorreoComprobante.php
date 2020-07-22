<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestReenvioCorreoComprobante extends FormRequest
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
    public function rules()
    {
        return [
            'id_factura' => 'required|exists:factura,id_factura',
            'correos' => ['nullable','string',function($attrbute,$value,$onFailure){
                if(isset($value)){
                    $correos = explode(',',$value);
                    foreach($correos as $correo){
                        if(trim($correo)!=""){
                            $valid = preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',trim($correo));
                            if($valid==0)
                                $onFailure('El correo '.$correo.' debe ser un formato válido');
                        }
                    }
                }
            }]
        ];
    }

    public function messages()
    {
        return [
            'id_factura.required' => 'No se obtuvo el idetificador de la factura',
            'id_factura.exists' => 'La factura no existe',
        ];
    }
}
