<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestStoreRetencionManualCliente;
use App\Model\{Cliente,
    DetalleImpuestosRetencion,
    DetalleRetencionCliente,
    Factura,
    ImpuestosRetencion,
    RetencionCliente};
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleXMLElement;
use Illuminate\Support\Facades\{Auth,File, DB};

class RetencionClienteController extends Controller
{
    public function inicio(){
        $usuario = Auth::user();

        return view('comprobantes.retenciones_clientes.inicio',[
            'clientes' => Cliente::where([
                ['id_usuario',$usuario->id_usuario],
                ['estado',true]
            ])->get(),

            'facturas' => Factura::where('id_usuario',$usuario->id_usuario)
                ->where(function ($q) use($usuario){

                    $retencionesCliente = RetencionCliente::join('factura as f','retencion_cliente.id_factura','f.id_factura')
                                            ->where('f.id_usuario',$usuario->id_usuario)->pluck('f.id_factura')->toArray();

                    $q->whereNotIn('id_factura',$retencionesCliente);

                })->select('id_factura','secuencial','id_cliente')->get(),

            'conceptosRetencion' => DB::table('detalle_impuesto_retencion as dir')
                                    ->join('impuestos_retencion as ir','dir.id_impuesto_retencion','ir.id_impuestos_retencion')
                                    ->select(DB::raw("concat(dir.codigo,' - ',dir.nombre) as nombre"),'dir.id_detalle_impuesto_retencion','dir.porcentaje','ir.codigo')
                                    ->orderBy('nombre','asc')->get()
        ]);
    }

    public function list(Request $request){
        return RetencionCliente::where('estado',$request->estado)
            ->whereBetween('fecha_emision',explode('~',$request->fechas))
            ->where(function($q) use($request){

                if(isset($request->id_cliente)){

                    $q->where('id_cliente',$request->id_cliente);

                }else{

                    $usuario = Auth::user();
                    $clientes = Cliente::where([
                        ['id_usuario',$usuario->id_usuario],
                        ['estado',true]
                    ])->pluck('id_cliente')->toArray();

                    $q->whereIn('id_cliente',$clientes);

                }

            })->orderBy('id_retencion_cliente','desc')
                ->with('detalles')->with('factura')->get();

    }

    public function procesarTxt(Request $request){

        $request->validate([
            'txt' => 'required|file|mimes:txt'
        ],[
            'txt.required' => 'El archivo txt es obligatorio',
            'txt.file' => ' El txt cargado debe ser un archivo válido',
            'txt.mimes' => 'El archivo Cargado debe tener la extensión .txt'
        ]);

        $arrClaveAcceso=[];
        $arrClaveAccesoFail=[];
        $archivo = File::get($request->txt);
        $usuario = Auth::user();

        $linea = explode("\n",trim($archivo));
        foreach ($linea as $item) {
            if($item!=""){
                $a = explode("\t",$item);
                if(isset($a[count($a)-2])){
                    $clave = $a[count($a)-2];
                    if(is_numeric($clave)){
                        if(strlen($clave)==49){
                            $arrClaveAcceso[] = $clave;
                        }else{
                            $arrClaveAccesoFail[]=$clave;
                        }
                    }
                }
            }
        }

        try{

            $data=[];
            $errors='';
            list($w,$x,$y,$z) = [0,0,0,0];
            $sinClaveAcceso='No existen las siguientes claves de acceso en el SRI: <br />';
            $sinFacturas = 'No existen facturas en el sistema asociadas para las siguientes retenciones: <br />';
            $sinClientes= 'No existen los siguientes clientes registrados: <br />';
            $noRetencion = 'Los siguientes números de comprobante no son ertenciones: <br />';

            foreach($arrClaveAcceso as $claveAcceso){
                $existClaveAcceso = RetencionCliente::where([
                    ['clave_acceso' ,$claveAcceso],
                    ['estado',true]
                ])->exists();

                if(!$existClaveAcceso){
                    $tipoDocumento = substr($claveAcceso, 8, 2);
                    if($tipoDocumento=="07"){ // RETENCIÓN
                        $response = $this->autorizacionComprobante($claveAcceso);
                        if($response->RespuestaAutorizacionComprobante->numeroComprobantes>0){

                            $dataXML = (String)$response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante;
                            $xml = new SimpleXMLElement($dataXML);

                            $cliente = Cliente::where('identificacion',(String)$xml->infoTributaria->ruc)->first();
                            $factura = Factura::where([
                                ['secuencial',(String)$xml->impuestos->impuesto->numDocSustento],
                                ['id_usuario',$usuario->id_usuario]
                            ])->first();

                            if(isset($cliente)){
                                if(isset($factura)){
                                    $req = new Request([
                                        'idCliente' => $cliente->id_cliente,
                                        'idUsuario' => $usuario->id_usuario,
                                        'idFactura' => $factura->id_factura,
                                        'razonSocial' => (String)$xml->infoTributaria->razonSocial,
                                        'ruc' => $cliente->identificacion,
                                        'secuencial' => (String)$xml->infoTributaria->estab.(String)$xml->infoTributaria->ptoEmi.(String)$xml->infoTributaria->secuencial,
                                        'dirMatriz' => (String)$xml->infoTributaria->dirMatriz,
                                        'fechaEmision' => (String)$xml->infoCompRetencion->fechaEmision,
                                        'dirEstablecimiento' => (String)$xml->infoTributaria->dirMatriz,
                                        'contriEsp' => (String)$xml->infoCompRetencion->contribuyenteEspecial,
                                        'obligContabilidad' => (String)$xml->infoCompRetencion->obligadoContabilidad,
                                        'claveAcceso' => (String)$xml->infoTributaria->claveAcceso,
                                        'tipoIdentSujRetenido' => (String)$xml->infoCompRetencion->tipoIdentificacionSujetoRetenido,
                                        'razonSocialSujRetenido' => (String)$xml->infoCompRetencion->razonSocialSujetoRetenido,
                                        'identSujRetenido' => (String)$xml->infoCompRetencion->identificacionSujetoRetenido,
                                        'peridoFiscal' => (String)$xml->infoCompRetencion->periodoFiscal,
                                        'fechaCont' => (String)$xml->infoCompRetencion->fechaEmision,
                                        'comentario' => '',
                                        'electronica' => true,
                                    ]);

                                    $retenciones=[];
                                    foreach($xml->impuestos->impuesto as $impuesto){
                                        $impuestoRetencion = ImpuestosRetencion::where('codigo',(String)$impuesto->codigo)->first();
                                        $detalleImpuestoRetencion = DetalleImpuestosRetencion::where('codigo',(String)$impuesto->codigoRetencion)->first();

                                        $retenciones[]=[
                                            'codigoTipoImpuesto' => $impuestoRetencion->id_impuestos_retencion,
                                            'codigoRetencion' => $detalleImpuestoRetencion->id_detalle_impuesto_retencion,
                                            'baseImponible'=> (String)$impuesto->baseImponible,
                                            'porcentRetenido' => (String)$impuesto->porcentajeRetener,
                                            'valorRetenido' => (String)$impuesto->valorRetenido,
                                            'codDocSustento' => (String)$impuesto->codDocSustento,
                                            'numDocSustento' => (String)$impuesto->numDocSustento,
                                            'fechaEmiDocSustento' => (String)$impuesto->fechaEmisionDocSustento
                                        ];
                                    }

                                    $req->query->add(['retenciones'=>$retenciones]);
                                    $data[] = $req->all();
                                }else{
                                    $x++;
                                    $sinFacturas.= (String)$xml->infoTributaria->estab.(String)$xml->infoTributaria->ptoEmi.(String)$xml->infoTributaria->secuencial.'<br />';
                                }
                            }else{
                                $y++;
                                $sinClientes.=  (String)$xml->infoTributaria->razonSocial.'<br />';
                            }
                        }else{
                            $z++;
                            $sinClaveAcceso.= $claveAcceso.'<br />';
                        }
                    }else{
                        $w++;
                        $noRetencion.= substr($claveAcceso, 24, 15);
                    }
                }
            }

            if($w=0)
                $errors.= $noRetencion;
            if($x>0)
                $errors.= $sinFacturas;
            if($y>0)
                $errors.= $sinClientes;
            if($z>0)
                $errors.= $sinClaveAcceso;

            session(['dataTxt' => $data]);

            return response()->json([
                'msg' => 'El archivo txt se ha analizado '. (($x>0 || $y>0 || $z>0 || $w>0) ? ('con las siguientes observaciones:<br /> '.$errors) : ''),
                'retencionesConsultadas' => $data,
                'x' => $x,
                'y' => $y,
                'z' => $z
            ]);

        }catch(\Exception $e){
            return response()->json([
                'errors'=>[
                    'mensaje'=> 'Se produjo un error al consultar la retenciones_clientes al sri, verifique su conexión a internet e intente nuevamente, 
                                 si el problema persite, se debe a que el web service del sri está indispuesto en este momento, proceda a intentarlo en unos minutos '.
                                 ', "'.$e->getMessage().'"'
                ]
            ],500);
        }

    }

    public function procesarXml(Request $request){

        dd($request->all());
    }

    public function storeRetencionManual(RequestStoreRetencionManualCliente $request){

        $cliente = Cliente::find($request->idCliente);
        $factura = Factura::find($request->idFactura);
        $usuario = Auth::user();

        $req = new Request([
            'idCliente' => $request->idCliente,
            'idUsuario' => $usuario->id_usuario,
            'idFactura' => $request->idFactura,
            'razonSocial' => $cliente->nombre,
            'ruc' => $cliente->identificacion,
            'secuencial' => $request->secuencial,
            'dirMatriz' => $cliente->direccion,
            'fechaEmision' => $request->fechaDoc,
            'dirEstablecimiento' => $cliente->direccion,
            //'contriEsp' => '',
            //'obligContabilidad' => '',
            'claveAcceso' => $request->nAutorizacion,
            'tipoIdentSujRetenido' => '04',
            'razonSocialSujRetenido' => $usuario->perfil->razon_social,
            'identSujRetenido' => $usuario->perfil->ruc,
            'peridoFiscal' => Carbon::parse($request->fechaDoc)->format('m/Y'),
            'fechaCont' => $request->fechaCont,
            'comentario' => $request->comentario,
            'electronica' => false,
        ]);

        $retenciones=[];
        foreach ($request->retenciones as $retencion){
            $ret = json_decode($retencion);
            $retenciones[]=[
                'codigoTipoImpuesto' => $ret->codigo_retencion,
                'codigoRetencion' => $ret->id_concepto_retencion,
                'baseImponible'=> $ret->base_imponible,
                'porcentRetenido' => $ret->porcentaje,
                'valorRetenido' => $ret->valor_retenido,
                'codDocSustento' => '01',
                'numDocSustento' => $factura->secuencial,
                'fechaEmiDocSustento' => $factura->fecha_doc
            ];
        }

        $req->query->add(['retenciones'=>$retenciones]);

        try{

            $response = $this->storeRetencionCliente($req);

            return response()->json([
                'retencionCliente'  => $response['retencionCliente'],
                'msg' => $response['msg'],
            ]);

        }catch(\Exception $e){

            $retencion = RetencionCliente::where('id_factura',$factura->id_factura)->first();
            if(isset($retencion))
                RetencionCliente::destroy($retencion->id_retencion_cliente);

            return HomeController::catch($e);
        }

    }

    public function anularRetencion(Request $request){

        $request->validate([
            'idRetencionCliente' => 'required|exists:retencion_cliente,id_retencion_cliente'
        ],[
            'idRetencionCliente.required' => 'No se obtuvo la retención del cliente',
            'idRetencionCliente.exists' => 'La retención enviada no existe'
        ]);

        try{
            $retencionCliente = RetencionCliente::find($request->idRetencionCliente);
            $retencionCliente->update([
                'estado' => false
            ]);

            return response()->json([
                'msg' => 'Se ha anulado la retención del cliente',
                'retencionCliente' => $retencionCliente
            ]);

        }catch(\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function removeRetencion(Request $request){

        $request->validate([
            'numero' => 'required'
        ],[
            'numero.required' => 'No se obtuvo el numero de retención a remover',
        ]);

        try{

            $data= session('dataTxt');
            $index = array_search($request->numero,array_column($data,'secuencial'));
            unset($data[$index]);
            session(['dataTxt'=> $data]);

            return response()->json([
                'msg' => 'Se ha removido la retención '.$request->numero
            ]);

        }catch(\Exception $e){
            return HomeController::catch($e);
        }

    }

    public function storeRetencionAsistido(Request $request){

        $retenciones = session('dataTxt');
        $savedRetenciones=[];
        $msg = 'Se han guardado las siguientes retenciones: <br />';
        foreach ($retenciones as $x => $retencion) {
            try{
                $req = new Request($retencion);
                $response = $this->storeRetencionCliente($req);
                $msg.= $req->secuencial;
                $savedRetenciones[]= $response['retencionCliente'];
            }catch(\Exception $e){
                return response()->json(['errors'=>['mensaje'=> $e->getMessage()]],500);
            }
        }

        return response()->json([
            'msg' => $msg,
            'retencionCliente' => $savedRetenciones
        ]);

    }

    public function storeRetencionCliente(Request $request){

        $existsRetencion = RetencionCliente::where([
            ['id_cliente',$request->idCliente],
            ['id_factura', $request->idFactura],
            ['estado',true]
        ])->exists();

        if(!$existsRetencion){
            RetencionCliente::create([
                'id_cliente' => $request->idCliente,
                'razon_social' => $request->razonSocial,
                'ruc' => $request->ruc,
                'secuencial' => $request->secuencial,
                'dir_matriz' => $request->dirMatriz,
                'fecha_emision' => $request->fechaEmision,
                'dir_establecimiento' => $request->dirEstablecimiento,
                'contriEsp' => $request->contriEsp,
                'obligContabilidad' => $request->obligContabilidad,
                'tipo_ident_suj_retenido' =>$request->tipoIdentSujRetenido,
                'razon_social_suj_retenido' => $request->razonSocialSujRetenido,
                'ident_suj_retenido' => $request->identSujRetenido,
                'periodo_fiscal' => $request->peridoFiscal,
                'id_factura' => $request->idFactura,
                'fecha_contabilidad' => $request->fechaCont,
                'electronica' => false,
                'clave_acceso' => $request->claveAcceso,
                'comentario' => $request->comentario
            ]);

            $ret = RetencionCliente::all()->last();

            foreach ($request->retenciones as $retencion) {

                DetalleRetencionCliente::create([
                    'id_retencion_cliente'=> $ret->id_retencion_cliente,
                    'codigo_tipo_impuesto' => $retencion['codigoTipoImpuesto'],
                    'codigo_retencion' => $retencion['codigoRetencion'],
                    'porcentaje_retenido' => $retencion['porcentRetenido'],
                    'base_imponible'=> $retencion['baseImponible'],
                    'valor_retenido' => $retencion['valorRetenido'],
                    'cod_doc_sustento' => $retencion['codDocSustento'],
                    'num_doc_sustento' => $retencion['numDocSustento'],
                    'fecha_emi_doc_sustento' => $retencion['fechaEmiDocSustento'],
                ]);

            }

            $ret->detalles;
            $ret->factura;

            return [
                'retencionCliente' => $ret,
                'msg' => 'Se ha guardado la retención del cliente'
            ];
        }else{
            throw new \Exception('Ya existe una retención recibida para la facutra '.$request->retenciones[0]['numDocSustento']);
        }


    }

}
