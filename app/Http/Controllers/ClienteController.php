<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RequestStoreCliente;
use App\Model\Cliente;
use App\Model\TipoIdentificacion;
use App\Model\TipoPago;
use App\Model\Pais;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function inicio()
    {
        return view('clientes.inicio',[
            'clientes' => Cliente::where('id_usuario',Auth::id())->orderby('nombre','asc')->get(),
            'tipoPago' => TipoPago::orderBy('nombre','asc')->get(),
            'tipoIdentificacion' => TipoIdentificacion::orderBy('nombre','asc')->get(),
            'paises' => Pais::orderBy('nombre','asc')->get()
        ]);
    }

    public function store(RequestStoreCliente $request){

        try{

            $data=[
                'id_tipo_identificacion'=> $request->id_tipo_identificacion,
                'id_usuario' => Auth::id(),
                'correo' =>$request->correo,
                'nombre' => $request->nombre,
                'id_tipo_pago' => $request->id_tipo_pago,
                'codigo_pais' => $request->codigo_pais,
                'tlf' => $request->tlf,
                'identificacion' => $request->identificacion,
                'direccion' => $request->direccion,
                'plazo_pago' => $request->plazo_pago,
                'ut_plazo_pago' => $request->ut_plazo_pago
            ];

            $cliente = Cliente::updateOrCreate(['id_cliente'=> $request->id_cliente],$data);
            $accion = isset($request->id_cliente) ? 'actualizado': 'creado';

            if(!isset($request->id_cliente))
                $cliente = Cliente::orderBy('id_cliente','desc')->first();

            return response()->json([
                'msg' => 'El cliente '.$cliente->nombre.' ha sido '.$accion,
                'idCliente' =>$cliente->id_cliente,
                'cliente' => $cliente
            ]);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function estado(Request $request){

        $request->validate([
            'idCliente' => 'required|exists:cliente,id_cliente',
        ],[
            'idCliente.required' => 'No se obtuvo el identificador del cliente',
            'idCliente.exists' => 'El cliente no está regitrado',
        ]);

        try{

            $cliente = Cliente::find($request->idCliente);
            $cliente->update(['estado'=> !$request->estado]);

            return response()->json([
                'msg' => 'El cliente '.$cliente->nombre.' ha sido '.($request->estado ? 'desactivado': 'activado')
            ],200);

        }catch(\Exception $e){

            return HomeController::catch($e);

        }

    }
}
