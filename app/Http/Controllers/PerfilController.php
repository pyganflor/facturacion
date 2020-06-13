<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestUpdateAccesos;
use App\Http\Requests\RequestUpdatePerfil;
use App\Model\UsuarioPerfil;
use Carbon\Carbon;
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

    public function updateAcessos(RequestUpdateAccesos $request){

        try{

            $usuario = Usuario::find(Auth::id());
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

            return response()->json(['msg'=>'Accesos actualizados!',],200);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function updatePerfil(RequestUpdatePerfil $request){

        try{
            //dd($request->all());
            $idUSuario = Auth::id();
            $perfil = UsuarioPerfil::find($idUSuario);
            if(!isset($perfil))
                $perfil = new UsuarioPerfil;

            $perfil->id_usuario = $idUSuario;
            $perfil->razon_social = $request->razonSocial;
            $perfil->nombre_comercial = $request->nombreComercial;
            $perfil->ruc = $request->ruc;
            $perfil->direc_matriz = $request->dirMatriz;
            $perfil->direc_establecimiento = $request->dirEstablecimiento;
            $perfil->contri_esp = $request->contriEsp;
            $perfil->oblig_cont = $request->obligadoContabilidad;

            if(isset($request->fileP12)) {

                $contenido = file_get_contents($request->file('fileP12'));

                openssl_pkcs12_read($contenido, $infoCert,$request->passFileP12);
                $dataFirma = openssl_x509_parse($infoCert['cert'],0);

                $archivo = $request->file('fileP12');
                $filep12 = mt_rand() . $archivo->getClientOriginalName();
                $disk = Storage::disk('filesP12');
                $perfil->firma_elec = $filep12;
                $perfil->pass_firma_elec = $request->passFileP12;
                $perfil->firma_desde = Carbon::parse($dataFirma['validFrom_time_t'])->format('Y-m-d');
                $perfil->firma_hasta = Carbon::parse($dataFirma['validTo_time_t'])->format('Y-m-d');
                $perfil->nombre_firma= $dataFirma['subject']['commonName'];
                $perfil->empresa_firma=$dataFirma['subject']['organizationName'];
                if (Auth::user()->perfil!=null)
                    if ($disk->exists(Auth::user()->perfil->firma_elec))
                        $disk->delete(Auth::user()->perfil->firma_elec);

                $disk->put($filep12, \File::get($archivo));
            }

            $perfil->save();

            return response()->json(['msg'=>'Perfil actualizado!'],200);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

    }
}
