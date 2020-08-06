<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestProcesarTxtRetencionCliente;
use App\Model\{Cliente, Factura, RetencionCliente};
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
                })->select('id_factura','secuencial')->get(),

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
                if(isset($request->id_cliente))
                    $q->where('id_cliente',$request->id_cliente);
            })->orderBy('id_retencion_cliente','desc')->get();

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
            $clavesFail='';
            foreach($arrClaveAcceso as $claveAcceso){
                $response = $this->autorizacionComprobante($claveAcceso);
                if($response->RespuestaAutorizacionComprobante->numeroComprobantes>0){
                    $dataXML = (String)$response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion->comprobante;
                    $xml = new SimpleXMLElement($dataXML);
                    dump($xml);
                }else{
                    $clavesFail.= $claveAcceso;
                }
            }
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

    public function storeRetencionManual(Request $request){

        dd($request->all());
    }

    public function storeRetencionAsistido(Request $request){

    }

    public function storeRetencionCliente($electroinca){

    }

}
