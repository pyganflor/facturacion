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
            'id_usuario' => ['required','exists:usuario,id_usuario',
                function($attribute,$value,$onFailure) use ($request){

                $usuario = Auth::user();
                $msg='';

                foreach ($usuario->modulos as $x=> $modulo) {
                    if($modulo->id_modulo == 1 && !isset($request->n_factura))
                        $msg.='El secuencial para las facutras es obligatorio<br>';

                    if($modulo->id_modulo == 2 && !isset($request->n_guia_remision))
                        $msg.='El secuencial para las guías de remisión es obligatorio<br>';

                    if($modulo->id_modulo == 3 && !isset($request->n_nota_debito))
                        $msg.='El secuencial para las notas de débito es obligatorio<br>';

                    if($modulo->id_modulo == 4 && !isset($request->n_nota_credito))
                        $msg.='El secuencial para las notas de crédito es obligatorio<br>';

                    if($modulo->id_modulo == 5 && !isset($request->n_retencion))
                        $msg.='El secuencial para las retenciones es obligatorio<br>';
                }

                if($x>0 && $msg!="")
                    $onFailure($msg);

                if(count($request->factureros)==0)
                    $onFailure('Debe ingresar al menos un facturero');

                if(count($request->ptoEmision)==0)
                    $onFailure('Debe ingresar al menos un punto de emisión');
            }]
        ];
    }

    public function messages()
    {
        return [
            'id_usuario.required' => 'No se obtuvo el identificador del usuario',
            'id_usuario.exists' => 'El usuario no se encuentra registrado',
            'entorno.required' => 'No se obtuvo el entorno en el que se van a hacer las autorizaciones en el SRI'
        ];
    }
}
