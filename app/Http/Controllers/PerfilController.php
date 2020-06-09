<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAccesos;
use Illuminate\Support\Facades\Auth;
use App\Model\Usuario;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function inicio(){
        return view('perfil.inicio',[
            'usuario'=> Auth::user()
        ]);
    }

    public function saveAcessos(SaveAccesos $request){

        try{

            $usuario = Usuario::updateOrCreate(
                [
                    'id_usuario' =>$request->idUSuario // BUSCAR POR EL ID DEL REGISTRO
                ],
                [
                    'nombre'=>$request->usuario,
                    'contrasena'=>$request->pass
                ]
            );
            //dump($usuario->id);  DEVUELVE EL ID INGRESADO O ACTUALIZADO

            return response()->json([
                'success'=>true,
                'usuario'=>$usuario,
            ],200);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }


    }
}
