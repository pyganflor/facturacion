<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use App\Model\Usuario;

class RequestUpdateAccesos extends FormRequest
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

                if(Auth::user()->nombre != $value)
                    if(Usuario::where('nombre',$request->usuario)->exists())
                        $onFailure('El '. $attribute. ' de usuario ya está en uso, por favor seleccione otro');
            },
            'pass'=>function($attribute,$value,$onFailure) use ($request){
                if(strlen($value)>0 && strlen($request->actualPass)>0){
                    if (Hash::check($request->actualPass,Auth::user()->contrasena)){
                        $valid = preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%*,.]{5,20}$/',$request->pass);
                        if($valid==0)
                            $onFailure('La nueva contraseña debe ser alfa númerica, y de mínimo 5 caracteres');
                    }else{
                        $onFailure('La contraseña actual no coincide, con la registrada');
                    }
                }elseif(strlen($request->actualPassss)>0 && strlen($value)<1){
                    $onFailure('Debe Colocar su nueva contraseña para actualizarla');
                }elseif(strlen($value)>0 && strlen($request->actualPass)<1){
                    $onFailure('Debe Colocar su contraseña actual para actualizar su nueva contraseña');
                }
            },
            'imagen'=>'sometimes|mimes:jpeg,png,JPEG,JPG,PNG|max:100'
        ];
    }

    public function messages()
    {
        return [
            'imagen.mimes'=>'La imagen debe ser en formato .jpeg .jpg .JPEG .png .PNG',
            'imagen.max' => 'La imagen debe pesar menos de 100KB'
        ];
    }
}
