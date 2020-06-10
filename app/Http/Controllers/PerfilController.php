<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestUpdteAccesos;
use Illuminate\Support\Facades\Auth;
use App\Model\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function inicio(){
        return view('perfil.inicio',[
            'usuario'=> Auth::user()
        ]);
    }

    public function updateAcessos(RequestUpdteAccesos $request){

        try{

            $usuario = Usuario::all()
                ->where('id_usuario',Auth::id())->first();
            $usuario->nombre = $request->usuario;

            if(isset($request->imagen)){
                $archivo = $request->file('imagen');
                $imagen  =  mt_rand().$archivo->getClientOriginalName();
                $disk = Storage::disk('img_user');

                if($disk->exists(Auth::user()->imagen))
                    $disk->delete(Auth::user()->imagen);

                $disk->put($imagen, \File::get($archivo));

                 $usuario->imagen = $imagen;
            }

            if(isset($request->pass))
                $usuario->contrasena = Hash::make($request->pass);


            $usuario->save();

            /*$usuario = Usuario::updateOrCreate(
                [
                    'id_usuario' =>Auth::id() // BUSCAR POR EL ID DEL REGISTRO
                ],
                [
                    'nombre'=>$request->usuario,
                    'contrasena'=> Hash::make($request->pass)
                ]
            );*/

            return response()->json([
                'msg'=>'Accesos actualizados!',
            ],200);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }


    }
}
