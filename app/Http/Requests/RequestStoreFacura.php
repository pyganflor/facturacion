<?php

namespace App\Http\Requests;

use App\Model\ArticuloCategoriaInventario;
use App\Model\Cliente;
use App\Model\Impuesto;
use App\Model\TipoImpuesto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RequestStoreFacura extends FormRequest
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
            'ptoEmision' => 'required|numeric|between:001,999',
            'facturero' => 'required|numeric|between:001,999',
            'fechaDoc' => ['required','date',function($attribute,$value,$onFailure) use ($request){
                if($value > now()->toDateString()){
                    $onFailure('La fecha de la factura no debe ser mayor al día de hoy');
                }else if(now()->diffInDays($value) > 7){
                    $onFailure('La fecha de la factura no debe ser de mas de 7 días pasados a la fecha actual');
                }else if($request->fechaVenc < now()->toDateString()){
                    $onFailure('La fecha de vencimiento de la factura no debe ser menor a la fecha actual');
                }
            }],
            'sustTributario'=> 'required|exists:sustento_tributario,id_sustento_tributario',
            'idCliente'=> 'required|exists:cliente,id_cliente',
            'total' => function($attribute,$value,$onFailure) use ($request){
                if($value>=200){
                    $cliente= Cliente::find($request->idCliente);
                    if($cliente->tipo_identificacion->codigo=='07')
                        $onFailure('La factura supera o es igual a los 200 USD de importe total por cuanto no puede ser emitida a nombre de CONSUMIDOR FINAL, debe cambiar el tipo de identificación del cliente');
                }
            },
            'correos'=> function($attribute,$value,$onFailure){
                $correos = explode(',',$value);
                foreach($correos as $correo){
                    if(trim($correo)!=""){
                        $valid = preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',trim($correo));
                        if($valid==0)
                            $onFailure('El correo del cliente'.$correo.' debe ser un formato válido');
                    }
                }
            },
            'formaPago' => 'required|numeric',
            'idTipoPago' => 'required|numeric|exists:tipo_pago,id_tipo_pago',
            'plazo' => 'required_if:formaPago,2',
            'undTiempoPlazo' =>  'required_if:formaPago,2',
            'articulos.*' =>function($attribute,$value,$onFailure){

                if(!ArticuloCategoriaInventario::where('id_articulo_categoria_inventario',$value['id_articulo'])->exists())
                    $onFailure('El artículo '. $value['nombre']. ' no existe');

                if($value['stock']==0)
                    $onFailure('No existe stock del artículo '. $value['nombre']. ' en el inventario');

                if(!isset($value['neto']) || $value['neto']=='')
                    $onFailure('No se obtuvo el valor del artículo '. $value['nombre']);

                foreach ($value['impuestos'] as $impuesto){

                    if(!Impuesto::where('id_impuesto',$impuesto['id_impuesto'])->exists())
                        $onFailure('El impuesto '. $impuesto['nombre_imp'] .' no existe');

                    if(!TipoImpuesto::where('id_tipo_impuesto',$impuesto['id_tipo_impuesto'])->exists())
                        $onFailure('El tipo de impuesto '. $impuesto['nombre_tipo_impuesto'] .' no existe');

                }

            }
        ];
    }

    public function messages()
    {
        return [
            'ptoEmision.required' => 'El punto de emisión es requerido',
            'ptoEmision.between' => 'El punto de emision debe estar entre 001 y 999',
            'ptoEmision.numeric' => 'El punto de emisión debe ser númerico',
            'facturero.required' => 'El facturero es requerido',
            'facturero.between' => 'El facturero debe estar entre 001 y 999',
            'facturero.numeric' => 'El facturero debe ser númerico',
            'fechaDoc' => 'La fecha de la factura es requerido',
            'fechaDoc.date' => 'La fecha de la factura debe ser un formato válido',
            'sustTributario.required'=> 'El sustento tributario es requerido',
            'sustTributario.exists' => 'El sutento tributario no existe',
            'idCliente.required'=> 'El cliente es requerido',
            'idCliente.exists' => 'El cliente no existe',
            'formaPago.required' => 'La forma de pago es requerida',
            'formaPago.numeric' => 'La forma de pago debe ser un número',
            'idTipoPago.required' => 'El tipo de pago es requerido',
            'idTipoPago.numeric' => 'El tipo de pago debe ser un número',
            'plazo.required_if' => 'El plazo de pago es obligatorio cuando la forma de pago es crédito',
            'undTiempoPlazo' => 'La unidad de tiempo de la forma de pago es obligatoria cuando la forma de pago es crédito'
        ];
    }
}
