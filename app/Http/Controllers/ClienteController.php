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
                'id_tipo_identificacion'=> $request->data['id_tipo_identificacion'],
                'id_usuario' => Auth::id(),
                'correo' =>$request->data['correo'],
                'nombre' => $request->data['nombre'],
                'id_tipo_pago' => $request->data['id_tipo_pago'],
                'codigo_pais' => $request->data['codigo_pais'],
                'tlf' => $request->data['tlf'],
                'identificacion' => $request->data['identificacion'],
                'direccion' => $request->data['direccion'],
                'plazo_pago' => $request->data['plazo_pago'],
                'ut_plazo_pago' => $request->data['ut_plazo_pago']
            ];

            $cliente = Cliente::updateOrCreate(['id_cliente'=> $request->data['id_cliente']],$data);
            $accion = isset($request->data['id_cliente']) ? 'actualizado': 'creado';

            if(!isset($request->data['id_cliente']))
                $cliente = Cliente::all()->last();

            return response()->json([
                'msg' => 'El cliente '.$cliente->nombre.' ha sido '.$accion,
                'idCliente' =>$cliente->id_cliente
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
