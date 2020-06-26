<?php

namespace App\Http\Requests;

use App\Model\Impuesto;
use App\Model\TipoImpuesto;
use Illuminate\Foundation\Http\FormRequest;

class RequestStoreInventario extends FormRequest
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
            'articulo'=> 'required|max:300',
            'id_categoria_inventario'=>'required|exists:categoria_inventario,id_categoria_inventario',
            'und'=> 'required',
            'neto' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'impuestos'=> ['required',function($attribute,$value,$onFailure){

                $iva= false;

                foreach ($value as $impuesto) {

                    $imp = json_decode($impuesto);

                    if (!$iva && $imp->id_impuesto == 1)
                        $iva = true;

                    if (!isset($imp->id_impuesto)) {
                        $onFailure('No se ha seleccionado el impuesto');
                    } else {
                        if (!Impuesto::where('id_impuesto', $imp->id_impuesto)->exists())
                            $onFailure('El impuesto no existe');
                    }

                    if (!isset($imp->id_tipo_impuesto)) {
                        $onFailure('No se ha seleccionado el tipo de impuesto');
                    } else {
                        if (!TipoImpuesto::where('id_tipo_impuesto', $imp->id_tipo_impuesto)->exists())
                            $onFailure('El tipo de impuesto no existe');
                    }
                }

                if(!$iva)
                    $onFailure('No se ha seleccionado el iva para el artículo');
            }]
        ];
    }

    public function messages()
    {
        return [
            'articulo.required'=> 'Debe escribir el nombre del artículo',
            'articulo.max' => 'El artículo debe tener hasta 300 caracteres',
            'id_categoria_inventario.required'=> 'Debe seleccionar la categpría del artículo',
            'id_categoria_inventario.exists' => 'La categoria seleccionada no existe',
            'und.required'=> 'Debe seleccionar el tipo de unidad de medida del artículo',
            'neto.required' => 'Debe escribir el precio del artículo',
            'neto.numeric' => 'El precio del artículo debe ser un número',
            'neto.min' => 'El precio del artículo debe ser mayor o igal a 0',
            'stock.required' => 'Debe escribir el stock del artículo',
            'stock.numeric' => 'El stock del artículo debe ser un número',
            'stock.min' => 'El stock del artículo debe ser mayor o igal a 0',
            'impuestos.required' => 'Si el artículo no tiene iva, seleccione la opción IVA -> Excento de iva'
        ];
    }
}
