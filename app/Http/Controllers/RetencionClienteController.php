<?php

namespace App\Http\Controllers;

use App\Model\{Cliente, Factura, RetencionCliente};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetencionClienteController extends Controller
{
    public function inicio(){
        $usuario = Auth::user();
        return view('clientes.retenciones.inicio',[
            'clientes' => Cliente::where([
                ['id_usuario',$usuario->id_usuario],
                ['estado',true]
            ])->get(),
            'facturas' => Factura::where('id_usuario',$usuario->id_usuario)
                ->where(function ($q) use($usuario){
                    $retencionesCliente = RetencionCliente::join('factura as f','retencion_cliente.id_factura','f.id_factura')
                        ->where('f.id_usuario',$usuario->id_usuario)->pluck('f.id_factura')->toArray();
                    $q->whereNotIn('id_factura',$retencionesCliente);
                })->select('id_factura','secuencial')->get()
        ]);
    }

    public function list(Request $request){

        return RetencionCliente::where('estado',$request->estado)
            ->whereBetween('fecha_doc',explode('~',$request->fechas))
            ->where(function($q) use($request){
                if(isset($request->id_cliente))
                    $q->where('id_cliente',$request->id_cliente);
            })->orderBy('n_factura','desc')->get();

    }
}
