<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStoreUsuario;
use App\Model\{Usuario, Modulo, UsuarioModulo};
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function inicio(){
        return view('usuarios.inicio',[
           'usuarios' => Usuario::orderBy('nombre','asc')->with('modulos')
                               ->leftJoin('usuario_perfil as up','usuario.id_usuario','up.id_usuario')
                                ->select('usuario.id_usuario','usuario.nombre','usuario.estado','up.razon_social','up.ruc','usuario.correo','usuario.tlf')->get(),
            'modulos' => Modulo::where('estado',true)->get()
        ]);
    }

    public function store(RequestStoreUsuario $request){
        try{
            //dd($request->all());

            $data=[
                'nombre'=> $request->nombre,
                'correo'=> $request->correo
            ];

            if(isset($request->contrasena))
                $data = Arr::collapse([$data,['contrasena'=> Hash::make($request->contrasena)]]);

            $usuario = Usuario::updateOrCreate(['id_usuario' => $request->idUsuario],$data);

            if(isset($request->idUsuario))
                UsuarioModulo::where('id_usuario',$request->idUsuario)->delete();

            if(!isset($usuario->id_usuario))
                $usuario = Usuario::orderBy('id_usuario','desc')->first();

            foreach ($request->modulos as $modulo) {
                UsuarioModulo::create([
                    'id_usuario' => $usuario->id_usuario,
                    'id_modulo' => !isset($modulo['id_modulo']) ? $modulo : $modulo['id_modulo']
                ]);
            }

            return response()->json([
                'msg' => 'Datos actulizados',
                'usuario' => Usuario::where('usuario.id_usuario',$usuario->id_usuario)->with('modulos')
                                ->leftJoin('usuario_perfil as up','usuario.id_usuario','up.id_usuario')
                                ->select('usuario.id_usuario','usuario.nombre','usuario.estado','up.razon_social','up.ruc','usuario.correo','usuario.tlf')->first()
            ]);


        }catch(\Exception $e){

            if(isset($usuario) && !isset($request->idUsuario))
                Usuario::destroy($usuario->id_usaurio);

            return HomeController::catch($e);
        }

    }

    public function estado(Request $request){

        $request->validate([
            'idUsuario' => 'required|exists:usuario,id_usuario',
        ]);

    }


}
