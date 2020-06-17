<?php

namespace App\Http\Requests;

use App\Model\UsuarioPerfil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestUpdatePerfil extends FormRequest
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
            'razonSocial' => 'required|max:300',
            'nombreComercial' => 'required|max:300',
            'ruc' => 'required|size:13',
            'dirMatriz' => 'required|max:300',
            'dirEstablecimiento' => 'required|max:300',
            'obligadoContabilidad' => 'required',
            'logoEmpresa'=>'sometimes|mimes:jpeg,png,JPEG,JPG,PNG|max:500',
            'fileP12' => function($attribute,$value,$onFailure) use ($request){

                $perfil = Auth::user()->perfil;
                $archivo = $request->file('fileP12');

                if((!isset($value) && !isset($perfil)) || (isset($request->passFileP12) && !isset($value)))
                    $onFailure('Debe cargar el archivo de la firma electrónica');

                if(isset($value) && !isset($request->passFileP12))
                    $onFailure('Debe escribir la contraseña del archivo de la firma electrónica');

                if(isset($value)  && $archivo->getClientOriginalExtension() != "p12")
                    $onFailure('El archivo de la firma electrónica debe tener la extensión .p12');

                if (isset($value) && !file_get_contents($value))
                    $onFailure('No se puede leer el arhivo de la firma electrónica, verifique que sea un archivo válido');

                if(isset($value)){
                    $contenido = file_get_contents($request->file('fileP12'));
                    openssl_pkcs12_read($contenido, $infoCert,$request->passFileP12);

                    if(!isset($infoCert))
                        $onFailure('No se puede leer el archivo asegurese que es la contraseña proporciada es la correspondiente y que el archivo cargado es el indicado');
                }

            }

        ];
    }

    public function messages()
    {
        return [
            'razonSocial.required'=> 'Debe escribir su razón social',
            'razonSocial.max'=> 'Su razón social no débe ser más de 300 carácteres',
            'nombreComercial.required'=> 'Debe escribir su nombre comercial',
            'nombreComercial.max'=> 'Su nombre comercial no débe ser más de 300 carácteres',
            'ruc.size' => 'Su Ruc debe ser de 13 caracteres',
            'dirMatriz.required'=> 'Debe escribir su razón social',
            'dirMatriz.max'=> 'Su dirección de establecimiento no débe ser más de 300 carácteres',
            'dirEstablecimiento.required'=> 'Debe escribir su dirección de establecimiento',
            'dirEstablecimiento.max'=> 'Su dirección de establecimiento no débe ser más de 300 carácteres',
            'obligadoContabilidad.required' => 'Debe seleccionar si es o no obligado a llevar contabilidad',
            'logoEmpresa.mimes'=>'El logo de la empresa debe ser en formato .jpeg .jpg .JPEG .png .PNG',
            'logoEmpresa.max' => 'El logo de la empresa debe pesar menos de 500KB'
        ];
    }
}
