<?php

namespace App\Http\Requests;

use App\Model\ArticuloCategoriaInventario;
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
            'formaPago' => 'required|numeric',
            'idTipoPago' => 'required|numeric|exists:tipo_pago,id_tipo_pago',
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
            'plazo' => 'required_if:formaPago,2',
            'undTiempoPlazo' =>  'required_if:formaPago,2',
            'articulos' =>function($attribute,$value,$onFailure){

                foreach ($value as $art) {

                    $articulo = json_decode($art);

                    if(!ArticuloCategoriaInventario::where('id_articulo_categoria_inventario',$articulo->id_articulo)->exists())
                        $onFailure('El artículo '. $articulo->nombre. ' no existe');

                    if($articulo->stock==0)
                        $onFailure('No existe stock del artículo '. $articulo->nombre. ' en el inventario');

                    if(!isset($articulo->neto) || $articulo->neto=='')
                        $onFailure('No se obtuvo el valor del artículo '. $articulo->nombre);

                    foreach ($articulo->impuestos as $impuesto){

                        if(!Impuesto::where('id_impuesto',$impuesto->id_impuesto)->exists())
                            $onFailure('El impuesto '. $impuesto->nombre_imp .' no existe');

                        if(!TipoImpuesto::where('id_tipo_impuesto',$impuesto->id_tipo_impuesto)->exists())
                            $onFailure('El tipo de impuesto '. $impuesto->nombre_tipo_impuesto .' no existe');

                    }
                }

            }
        ];
    }
}
