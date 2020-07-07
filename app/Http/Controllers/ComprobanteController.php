<?php

namespace App\Http\Controllers;

use App\Model\Inventario;
use App\Model\SustentoTributario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ComprobanteController extends Controller
{
    public function inicio(Request $request){

        $usuario = Auth::user();
        /*FacturaController::getFacturas();*/

        return view('comprobantes.inicio',[
            'modulos' => $usuario->modulos,
            'clientes' => $usuario->clientes,
            'factureros' => $usuario->factureros,
            'sustentoTributario' => SustentoTributario::get(),
            'inventario' => Inventario::where('id_usuario',$usuario->id_usuario)->with('categorias')->first()
        ]);
    }
}
