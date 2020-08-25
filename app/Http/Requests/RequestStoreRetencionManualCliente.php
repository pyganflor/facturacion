<?php

namespace App\Http\Requests;

use App\Model\DetalleImpuestosRetencion;
use App\Model\ImpuestosRetencion;
use App\Model\RetencionCliente;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestStoreRetencionManualCliente extends FormRequest
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
        dd($request->all());
        return [
            'idFactura' => 'required|exists:factura,id_factura',
            'idCliente' => 'required|exists:cliente,id_cliente',
            'secuencial' => ['required','max:15','min:15',
                function($attribute,$value,$onFailure) use ($request){
                    $usuario = Auth::user();

                    if(!isset($usuario->perfil))
                        $onFailure('Debe completar sus datos de perfil antes de cargar una retención');

                    $retencion = RetencionCliente::where([
                        ['retencion_cliente.secuencial', $value],
                        ['retencion_cliente.estado', true],
                        ['f.id_usuario',$usuario->id_usuario],
                        ['retencion_cliente.id_cliente',$request->idCliente]
                    ])->join('factura as f','retencion_cliente.id_factura','f.id_factura')->exists();

                    if($retencion)
                        $onFailure('Ya existe una retención registrada con el número de documento ingresado');
                }
            ],
            'nAutorizacion' => ['required',
                function($attribute,$value,$onFailure){
                    if(RetencionCliente::where([
                        ['clave_acceso', $value],
                        ['estado', true]
                    ])->exists())
                        $onFailure('Ya existe una retención registrada con el número de autorización ingresado');
                }
            ],
            'retenciones.*'=>['required',function($attribute,$value,$onFailure){
                    $retencion = json_decode($value);
                    $nRetencion = explode(".",$attribute)[1]+1;

                    $impuestoRetencion = ImpuestosRetencion::where('codigo',$retencion->codigo_retencion)->exists();
                    if(!$impuestoRetencion)
                        $onFailure('El código del tipo de retención en la retención '.$nRetencion.' no existe');

                    $detalleImpuestoRetencion = DetalleImpuestosRetencion::find($retencion->id_concepto_retencion)->exists();
                    if(!$detalleImpuestoRetencion)
                        $onFailure('El impuesto de la retención '.$nRetencion.' no existe');

                    if(!isset($retencion->base_imponible) || $retencion->base_imponible<0)
                        $onFailure('La base imponible de la retención '.$nRetencion.' es obligatoria y debe ser mayor a 0');

                    if(!isset($retencion->porcentaje))
                        $onFailure('El porcentaje de la retención '.$nRetencion.' es obligatoria');

                    if(!isset($retencion->valor_retenido))
                        $onFailure('El valor retenido de la retención '.$nRetencion.' es obligatoria');

            }],
            'fechaDoc' => 'required|date',
            'fechaCont' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'idCliente.required' => 'El cliente es obligatorio',
            'idCliente.exists' => 'El cliente no existe',
            'secuencial.required' => 'El secuencial de la factura retenida es obligatorio',
            'fechaDoc.required' => 'La fecha de emisión de la retención es obligatoria',
            'fechaDoc.date' => 'La fecha emisión debe ser un fecha válida',
            'nAutorizacion.required' => 'El número de autorización de la retención es obligatorio',
            'idFactura.required' => 'La factura es obligatoria',
            'idFactura.exists' => 'La factura no existe',
            'fechaCont.required' => 'La fecha de contabilidad es obligatoria',
            'fechaCont.date' => 'La fecha de contabilidad debe ser una fecha válida',
        ];
    }
}
