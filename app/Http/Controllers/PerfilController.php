<?php

namespace App\Http\Controllers;

use App\Http\Requests\{RequestStoreInfoAdicional, RequestUpdateAccesos, RequestUpdatePerfil};
use App\Model\{UsuarioFacturero, UsuarioPerfil, Usuario, UsuarioPtoEmision};
use Illuminate\Support\Facades\{Auth,Hash,Storage};
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function inicio(){
        return view('perfil.inicio',[
            'usuario'=> Usuario::where('id_usuario',Auth::user()->id_usuario)
                                ->with('perfil')->with('modulos')->with('ptoEmision')->with('factureros')->first(),
            'storage' => Storage::url('img_user')
        ]);
    }

    public function updateAcessos(RequestUpdateAccesos $request){

        try{

            $usuario = Usuario::find(Auth::id());
            $usuario->nombre = $request->usuario;
            $usuario->correo = $request->correo;
            $usuario->tlf = isset($request->tlf) ? $request->tlf : null;

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

            return response()->json(['msg'=>'Accesos actualizados!',],200);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function updatePerfil(RequestUpdatePerfil $request){

        try{
            $usuario = Auth::user();

            $data =[
                'id_usuario' => $usuario->id_usuario,
                'razon_social' => $request->razonSocial,
                'nombre_comercial' => $request->nombreComercial,
                'ruc' => $request->ruc,
                'direc_matriz' => $request->dirMatriz,
                'direc_establecimiento' => $request->dirEstablecimiento,
                'oblig_cont' => $request->obligadoContabilidad
            ];

            if(isset($request->contriEsp) && $request->contriEsp!=="null")
                $data = Arr::collapse([$data,['contri_esp' => $request->contriEsp]]);

            if(isset($request->fileP12)) {
                $contenido = file_get_contents($request->file('fileP12'));

                openssl_pkcs12_read($contenido, $infoCert,$request->passFileP12);
                $dataFirma = openssl_x509_parse($infoCert['cert'],0);

                $archivo = $request->file('fileP12');
                $filep12 = mt_rand() . $archivo->getClientOriginalName();
                $disk = Storage::disk('filesP12');
                $dataP12= [
                    'firma_elec' => $filep12,
                    'pass_firma_elec'=> $request->passFileP12,
                    'firma_desde' =>  Carbon::parse($dataFirma['validFrom_time_t'])->format('Y-m-d'),
                    'firma_hasta' =>  Carbon::parse($dataFirma['validTo_time_t'])->format('Y-m-d'),
                    'nombre_firma'=>  $dataFirma['subject']['commonName'],
                    'empresa_firma'=> $dataFirma['subject']['organizationName'],
                ];
                if ($usuario->perfil!=null)
                    if ($disk->exists($usuario->perfil->firma_elec))
                        $disk->delete($usuario->perfil->firma_elec);

                $disk->put($filep12, \File::get($archivo));

                $data = Arr::collapse([$data,$dataP12]);
            }


            if(isset($request->logoEmpresa)) {

                $archivo = $request->file('logoEmpresa');
                $imagen = mt_rand() . $archivo->getClientOriginalName();
                $disk = Storage::disk('logo_empresa');
                if (Auth::user()->perfil!=null)
                    if ($disk->exists($usuario->perfil->logo_empresa))
                        $disk->delete($usuario->perfil->logo_empresa);

                $disk->put($imagen, \File::get($archivo));

                $data = Arr::collapse([$data,['logo_empresa' => $imagen]]);
            }

            $perfil = UsuarioPerfil::updateOrCreate(['id_usuario' => Auth::id()],$data);

            return response()->json([
                'msg'=>'Perfil actualizado!',
                'nombreFirma' => $perfil->nombre_firma,
                'empresaFirma' => $perfil->empresa_firma,
                'firmaDesde' => $perfil->firma_desde,
                'firmaHasta' => $perfil->firma_hasta
            ],200);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function storeInfoAdicional(RequestStoreInfoAdicional $request){

        try{

            $perfil = UsuarioPerfil::updateOrCreate(
                ['id_usuario'=>$request->id_usuario],
                [
                    'n_factura' => str_pad($request->n_factura, 9, '0', STR_PAD_LEFT),
                    'n_guia_remision' => str_pad($request->n_guia_remision, 9, '0', STR_PAD_LEFT),
                    'n_retencion' => str_pad($request->n_retencion, 9, '0', STR_PAD_LEFT),
                    'n_nota_debito' => str_pad($request->n_nota_debito, 9, '0', STR_PAD_LEFT),
                    'n_nota_credito' => str_pad($request->n_nota_credito, 9, '0', STR_PAD_LEFT),
                    "entorno" => $request->entorno == "true" ? 2 : 1
                ]
            );


            $oldFactureros = UsuarioFacturero::where('id_usuario',$request->id_usuario)->pluck('id_usuario_facturero')->toArray();
            $oldptosEmision = UsuarioPtoEmision::where('id_usuario',$request->id_usuario)->pluck('id_usuario_pto_emision')->toArray();

            foreach ($request->factureros as $x=> $facturero) {
                $f = json_decode($facturero);
                UsuarioFacturero::create([
                    'id_usuario' => $request->id_usuario,
                    'numero' => str_pad($f->numero, 3, '0', STR_PAD_LEFT)
                ]);
            }

            foreach ($request->ptoEmision as $y => $ptoEmision) {
                $p = json_decode($ptoEmision);
                UsuarioPtoEmision::create([
                    'id_usuario' => $request->id_usuario,
                    'numero' => str_pad($p->numero, 3, '0', STR_PAD_LEFT)
                ]);
            }

            if(($x+1) === count($request->factureros))
                UsuarioFacturero::destroy($oldFactureros);
            if(($y+1) === count($request->ptoEmision))
                UsuarioPtoEmision::destroy($oldptosEmision);

            return response()->json([
                'msg' => 'Se han guardado los datos adicionales',
                'perfil' => $perfil
            ]);

        }catch (\Exception $e){
            return HomeController::catch($e);
        }


    }
}
