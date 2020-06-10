<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestLogin extends FormRequest
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
            'usuario' => 'required|min:6|exists:usuario,nombre',
            'contrasena' => 'required|min:5|alpha_num'
        ];
    }

    public function messages()
    {
        return [
            'usuario.required'=>'El usuario es obligatorio',
            'usuario.exists'=>'El usuario no existe',
            'usuario.min'=>'El usuario debe tener mínimo 5 caracteres',
            'contrasena.required'=>'La contraseña es obligatoria',
            'contrasena.min'=>'La contraseña debe tener mínimo 4 caracteres',
            'contrasena.alpha_num'=>'La contraseña debe ser alfa núerica',
        ];
    }
}
