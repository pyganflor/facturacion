<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStoreProveedor;
use Illuminate\Http\Request;
use App\Model\Proveedor;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    public function inicio(){
        return view('proveedores.inicio',[
            'proveedores' => Proveedor::where('id_usuario',Auth::user()->id_usuario)->get()
        ]);
    }

    public function store(RequestStoreProveedor $request){

        try{

            $proveedor = Proveedor::updateOrCreate(
                ['id_proveedor'=>$request->id_proveedor],
                [
                    'nombre_comercial'=> $request->nombre_comercial,
                    'identificacion'=> $request->identificacion,
                    'razon_social'=> $request->razon_social,
                    'tlf'=> $request->tlf,
                    'correo'=> $request->correo,
                    'direccion'=> $request->direccion,
                    'banco'=> $request->banco,
                    'tipo_cta'=> $request->tipo_cta,
                    'numero_cta'=> $request->numero_cta,
                    'id_usuario' => Auth::user()->id_usuario
                ]
            );

            if(!isset($proveedor->id_proveedor))
                $proveedor = Proveedor::orderBy('id_proveedor','desc')->first();

            return response()->json([
               'msg' => 'Los datos del proveedor han sido guardados',
                'proveedor' => $proveedor
            ]);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function estado(Request $request)
    {
        $request->validate([
            'idProveedor' => 'required|exists:proveedor,id_proveedor',
        ],[
            'idProveedor.required' => 'No se obtuvo el identificador del proveedor',
            'idProveedor.exists' => 'El proveedor no está regitrado',
        ]);

        try{

            $proveedor = Proveedor::find($request->idProveedor);
            $proveedor->update(['estado'=> !$request->estado]);

            return response()->json([
                'msg' => 'El proveedor '.$proveedor->nombre.' ha sido '.($request->estado ? 'desactivado': 'activado')
            ],200);

        }catch(\Exception $e){
            return HomeController::catch($e);
        }
    }
}
