<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Model\{
    Cliente,
    SustentoTributario,
    tipoPago,
    Inventario
};
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RequestStoreFacura;
use DomDocument;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{

    public function inicio(Request $request){

        $usuario = Auth::user();

        return view('comprobantes.factura.inicio',[
            'clientes' => Cliente::where([
                ['id_usuario',$usuario->id_usuario],
                ['estado',true]
            ])->get(),
            'factureros' => $usuario->factureros,
            'sustentoTributario' => SustentoTributario::get(),
            'puntoEmision' => $usuario->ptoEmision,
            'tiposPago' => tipoPago::get(),
            'inventario' => Inventario::where('id_usuario',$usuario->id_usuario)->with('categoriasActivadas')->first()
        ]);

    }

    public function store(RequestStoreFacura $request){

        //dd($request->all());

        try{

            $msg = '';
            $usuario = Auth::user();
            $cliente = Cliente::find($request->idCliente);
            $serie = $request->ptoEmision.$request->facturero;
            $codigoNumerico = 12345678;
            $arrFecha = explode('-',$request->fechaDoc);
            $fecha = $arrFecha[2].$arrFecha[1].$arrFecha[0];
            $cadena = $fecha . '01' . $usuario->perfil->ruc . $usuario->perfil->entorno . $serie . $usuario->perfil->n_factura . $codigoNumerico . 1;
            $digitoVerificador = $this->digitoVerificador($cadena);

            if(!$digitoVerificador)
                return response()->json([
                    'errors'=>['Mensaje:'=> ' La clave de acceso está mal formada, verifique que su información adicional este correctamente ingresada en la configuración de su perfil y vuelva a intentarlo']
                ],500);

            $xml = new DomDocument('1.0', 'UTF-8');
            $factura = $xml->createElement('factura');
            $factura->setAttribute('id', 'comprobante');
            $factura->setAttribute('version', '1.0.0');
            $xml->appendChild($factura);
            $infoTributaria = $xml->createElement('infoTributaria');
            $factura->appendChild($infoTributaria);
            $claveAcceso = $cadena . $digitoVerificador;

            $informacionTributaria = [
                'ambiente' => $usuario->perfil->entorno,
                'tipoEmision' => 1,
                'razonSocial' => $usuario->perfil->razon_social,
                'nombreComercial' => $usuario->perfil->nombre_comercial,
                'ruc' => $usuario->perfil->ruc,
                'claveAcceso' => $claveAcceso,
                'codDoc' => '01',
                'estab' => $request->ptoEmision,
                'ptoEmi' => $request->facturero,
                'secuencial' => $usuario->perfil->n_factura,
                'dirMatriz' => $usuario->perfil->direc_matriz
            ];

            foreach ($informacionTributaria as $key => $it) {
                $nodo = $xml->createElement($key, $it);
                $infoTributaria->appendChild($nodo);
            }

            $infoFactura = $xml->createElement('infoFactura');
            $factura->appendChild($infoFactura);
            $informacionFactura = [
                'fechaEmision' => Carbon::parse($request->fechaDoc)->format('d/m/Y'),
                'dirEstablecimiento' => $usuario->perfil->direc_establecimiento,
                'obligadoContabilidad' => $usuario->perfil->oblig_cont,
                'tipoIdentificacionComprador' => $cliente->tipo_identificacion->codigo,
                'razonSocialComprador' => $cliente->identificacion == "9999999999999" ? "CONSUMIDOR FINAL" : $cliente->nombre,
                'identificacionComprador' => $cliente->identificacion,
                'totalSinImpuestos' => $request->subTotal,
                'totalDescuento' => number_format($request->descuento, 2, ".", ""),
            ];

            foreach ($informacionFactura as $key => $if) {
                $nodo = $xml->createElement($key, $if);
                $infoFactura->appendChild($nodo);
            }

            $totalConImpuestos = $xml->createElement('totalConImpuestos');
            $infoFactura->appendChild($totalConImpuestos);

            foreach ($request->articulos as $articulo) {
                $articulo = json_decode($articulo);
                foreach ($articulo->impuestos as $impuesto) {

                    $informacionImpuestos = [
                        'codigo' => $impuesto->codigo_imp,
                        'codigoPorcentaje' => $impuesto->codigo_tipo_imp,
                        'baseImponible' => number_format($impuesto->base_imponible, 2, ".", ""),
                        'valor' => number_format($impuesto->valor_imp, 2, ".", "")
                    ];
                    $totalImpuesto = $xml->createElement('totalImpuesto');
                    $totalConImpuestos->appendChild($totalImpuesto);
                    foreach ($informacionImpuestos as $key => $iI) {
                        $nodo = $xml->createElement($key, $iI);
                        $totalImpuesto->appendChild($nodo);
                    }
                }
            }

            $propina = $xml->createElement('propina', '0.00');
            $infoFactura->appendChild($propina);

            $importeTotal = $xml->createElement('importeTotal', number_format($request->total, 2, ".", ""));
            $infoFactura->appendChild($importeTotal);

            $moneda = $xml->createElement('moneda', 'DOLAR');
            $infoFactura->appendChild($moneda);

            $pagos = $xml->createElement('pagos');
            $infoFactura->appendChild($pagos);

            $pago = $xml->createElement('pago');

            $pagos->appendChild($pago);

            $codigoTipoPago = 20;
            if($request->formaPago==1){
                $tipoPago = TipoPago::find($request->idTipoPago);
                $codigoTipoPago = $tipoPago->codigo;
            }

            if($request->undTiempoPlazo == 0)
                $plazo = 'Días';
            else if($request->undTiempoPlazo == 1)
                $plazo = 'Semanas';
            else
                $plazo = 'Meses';

            $infoPagos=[
                'formaPago' => $codigoTipoPago,
                'total' => $request->total,
                'plazo' => $request->plazo,
                'unidadTiempo' => $plazo
            ];

            foreach ($infoPagos as $key => $ip) {
                $nodo = $xml->createElement($key, $ip);
                $pago->appendChild($nodo);
            }

            $detalles = $xml->createElement('detalles');
            $factura->appendChild($detalles);
            foreach ($request->articulos as $x => $articulo) {

                $articulo = json_decode($articulo);

                $detalle = $xml->createElement('detalle');
                $detalles->appendChild($detalle);
                $informacionDetalle = [
                    'codigoPrincipal' => $articulo->codigo_p,
                    'descripcion' => $articulo->nombre,
                    'cantidad' => $articulo->cantidad,
                    'precioUnitario' => number_format($articulo->neto, 2, ".", ""),
                    'descuento' => number_format($articulo->descuento, 2, ".", ""),
                    'precioTotalSinImpuesto' => number_format($articulo->total, 2, ".", "")
                ];
                foreach ($informacionDetalle as $key => $iD) {
                    $nodo = $xml->createElement($key, $iD);
                    $detalle->appendChild($nodo);
                }

                $impuestos = $xml->createElement('impuestos');
                $detalle->appendChild($impuestos);

                foreach ($articulo->impuestos as $imp) {
                    $impuesto = $xml->createElement('impuesto');
                    $impuestos->appendChild($impuesto);
                    $informacionImpuesto = [
                        'codigo' => $imp->codigo_imp,
                        'codigoPorcentaje' => $imp->codigo_tipo_imp,
                        'tarifa' => number_format($imp->tarifa, 2, ".", ""),
                        'baseImponible' => number_format($articulo->total, 2, ".", ""),
                        'valor' => number_format($imp->valor_imp, 2, ".", "")
                    ];
                    foreach ($informacionImpuesto as $key => $iIp) {
                        $nodo = $xml->createElement($key, $iIp);
                        $impuesto->appendChild($nodo);
                    }
                }

            }

            $informacionAdicional = $xml->createElement('infoAdicional');
            $factura->appendChild($informacionAdicional);

            $campos_adicionales = [
                'Dirección'=> $usuario->perfil->direc_establecimiento,
                'Email'    => $usuario->correo
            ];

            foreach ($campos_adicionales as $key => $ca) {
                $campoAdicional = $xml->createElement('campoAdicional', $ca);
                $campoAdicional->setAttribute('nombre', $key);
                $informacionAdicional->appendChild($campoAdicional);
            }

            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;
            $stringXml = $xml->saveXML();
            $xml = "fact_".$request->ptoEmision.$request->facturero.$usuario->perfil->n_factura.'.xml';
            $pathFacturas = storage_path('app/public/xml/facturas/');
            $carpetaPersonal = $usuario->id_usuario.'/'.Carbon::now()->format('Y_m');

            $save = Storage::disk('xml_generado')->put($carpetaPersonal.'/'.$xml, $stringXml);
            $pathFirmados = $pathFacturas.'firmado'.'/'.$carpetaPersonal.'/';
            $pathEnviados = $pathFacturas.'enviado'.'/'.$carpetaPersonal.'/';
            $pathAutorizados = $pathFacturas.'autorizado'.'/'.$carpetaPersonal.'/';
            $pathRechazados = $pathFacturas.'rechazado'.'/'.$carpetaPersonal.'/';
            $pathGenerados = $pathFacturas.'generado'.'/'.$carpetaPersonal.'/';
            $pathNoAutorizados = $pathFacturas.'no_autorizado'.'/'.$carpetaPersonal.'/';

            $wsdlRecepcion = $usuario->perfil->entorno == 1  ? env('WSDL_PRUEBAS_RECEPCION') : env('WSDL_PRODUCCION_RECEPCION');
            $wsdlAutorizacion = $usuario->perfil->entorno == 1  ? env('WSDL_PRUEBAS_AUTORIZACION') : env('WSDL_PRODUCCION_AUTORIZACION');

            if(!file_exists($pathFirmados)) {
                if (
                    !mkdir($pathFirmados, 0775, true) ||
                    !mkdir($pathEnviados, 0775, true) ||
                    !mkdir($pathAutorizados, 0775, true) ||
                    !mkdir($pathRechazados, 0775, true) ||
                    !mkdir($pathNoAutorizados, 0775, true)
                )
                    return response()->json([
                        'errors' => ['Mensaje:' => 'No se pudo crear el directorio para guardar el xml firmado, intente nuevamente, si el problema persiste contacte al área de sistemas']
                    ], 500);
            }

            $data =[
                'carpetaGenerados' => $pathGenerados,
                'xml' => $xml,
                'carpetaFirmardos' => $pathFirmados,
                'contrasena' => $usuario->perfil->pass_firma_elec,
                'archivoFirmaDigital' => storage_path('app/public/filesp12'),
                'nombreArchivoFirmaDigital' =>$usuario->perfil->firma_elec
            ];

            if ($save) {
                $resultado = $this->firmarXml($data);
                $msg .= $this->mensajeFirmaXml($resultado,$data['xml']).'<br />';
                if ($resultado) {

                    $data=[
                        'xml' => $xml,
                        'carpetaFirmardos' => $pathFirmados,
                        'carpetaEnviados' => $pathEnviados,
                        'carpetaRechazados' => $pathRechazados,
                        'carpetaAutorizados' =>$pathAutorizados,
                        'carpetaNoAutorizados' =>$pathNoAutorizados,
                        'wsdlRecepcion' =>$wsdlRecepcion,
                        'wsdlAutorizacion' => $wsdlAutorizacion,
                        'claveAcceso' => $claveAcceso
                    ];

                    $resultado= $this->enviarXml($data);

                    if($resultado){
                        switch($resultado[0]){
                            case '0':
                                //ENVIADO PERO RECHAZADO, BUSCAR ARCHIVO EN LA CARPETA RECHAZADOS CORRESPONDIENTE

                                break;
                            case '1':
                                $class = "success";
                                //ENVIADO PERO RECHAZADO, Y AUTORIZADO
                                break;
                            case '2':
                                $class = "danger";
                                //FALLA DE CONEXIÓN CON WL WEB SERVICE
                                break;
                        }
                        $msg .= $this->mensajeEnvioXml($resultado[0]);
                    }else{
                        return response()->json([
                            'errors'=>['Mensaje:'=> 'No se pudo enviar el xml generado al sri, busque el registro en la vista de facturas e intente enviarlo nuevamente, si el problema persiste contacte al área de sistemas']
                        ],500);
                    }

                }else{
                    return response()->json([
                        'errors'=>[
                            'Mensaje:'=> 'El sistema informa de los siguientes mensajes <br />', $msg,
                        ]
                    ],500);
                }
            }else{
                return response()->json([
                    'errors'=>['Mensaje:'=> 'No se pudo guardar el xml generado, intente nuevamente, si el problema persiste contacte al área de sistemas']
                ],500);
            }

            return response()->json([
                'msg' => $msg
            ],200);

        }catch(\Exception $e){

            return HomeController::catch($e);
        }
    }


}
