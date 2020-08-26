<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Model\{
    Inventario,
    SustentoTributario,
    Proveedor
};
use Illuminate\Http\Request;

class FacturaProveedorController extends Controller
{
    public function inicio(){

        $idUsuario = Auth::id();
        return view('comprobantes.facturas_proveedores.inicio',[
            'sustentoTributario' => SustentoTributario::orderBy('nombre','asc')->get(),
            'proveedores' => Proveedor::where([
                ['id_usuario',$idUsuario],
                ['estado',true]
            ])->orderBy('razon_social','asc')->get(),
            'inventario' => Inventario::where('id_usuario',$idUsuario)->with('categorias')->first(),
        ]);
    }
}
