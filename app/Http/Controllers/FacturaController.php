<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\{Cliente, SustentoTributario,tipoPago,Inventario};
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{

    public static function inicio(Request $request){

        $usuario = Auth::user();

        return view('comprobantes.factura.inicio',[
            'clientes' => Cliente::where([
                ['id_usuario',$usuario->id_usuario],
                ['estado',true]
            ])->get(),
            'factureros' => $usuario->factureros,
            'sustentoTributario' => SustentoTributario::get(),
            'puntoEmision' => $usuario->ptoEmision,
            'tiposPago' => tipoPago::get(),
            'inventario' => Inventario::where('id_usuario',$usuario->id_usuario)->with('categoriasActivadas')->first()
        ]);

    }


}
