<?php

namespace App\Http\Controllers;

use App\Http\Requests\{
    RequestReenvioCorreoComprobante,
    RequestValidaIdComprobante,
    RequestStoreFacura
};
use App\Model\{ArticuloCategoriaInventario,
    ArticuloFactura,
    Cliente,
    DetalleFactura,
    FacturaPago,
    ImpuestoArticuloFactura,
    ImpuestoDetalleFactura,
    SustentoTributario,
    TipoImpuesto,
    TipoPago,
    Inventario,
    Factura,
    Usuario,
    UsuarioFacturero,
    TipoIdentificacion,
    Pais};
use Illuminate\Support\Facades\{Storage,Auth};
use App\Jobs\pdf\PdfFactura;
use App\Jobs\xml\XmlRespuestaFactura;
use DomDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;


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
            'sustentoTributario' => SustentoTributario::orderBy('nombre','asc')->get(),
            'puntoEmision' => $usuario->ptoEmision,
            'tiposPago' => TipoPago::orderBy('nombre','asc')->get(),
            'tipoIdentificacion' => TipoIdentificacion::orderBy('nombre','asc')->get(),
            'paises' => Pais::orderBy('nombre','asc')->get(),
            'inventario' => Inventario::where('id_usuario',$usuario->id_usuario)->with('categoriasActivadas')->first()
        ]);

    }

    public function list(Request $request){
        $usuario = Auth::user();

        return Factura::where([
            ['factura.estado',$request->estado],
            ['factura.id_usuario',$usuario->id_usuario]
        ])->where(function($q) use($usuario){
            if(isset($usuario->perfil)){
                $q->where('entorno',$usuario->perfil->entorno);
            }
        })->join('cliente as c','factura.id_cliente','c.id_cliente')
            ->join('detalle_factura as df','factura.id_factura','df.id_factura')
            ->select(
                'factura.id_factura',
                'factura.secuencial',
                'factura.clave_acceso',
                'factura.total',
                'factura.estado',
                'c.nombre as cliente',
                'factura.fecha_doc',
                'factura.fecha_aut',
                'factura.entorno',
                'factura.causa',
                'factura.carpeta',
                'factura.id_usuario',
                'factura.correos',
                'df.importe_total as total',
                'df.razon_social_emisor as razon_social'
            )->where(function($q) use ($request) {
                if(isset($request->id_cliente))
                    $q->where('c.id_cliente',$request->id_cliente);

                if(isset($request->fechas)){
                    $fecha= explode("~",$request->fechas);
                    $q->whereBetween('fecha_doc',$fecha);
                }
            })->orderBy('secuencial','desc')->get();
    }

    public function store(RequestStoreFacura $request){

        try{
            $msg = '';
            $success=true;
            $usuario = Auth::user();
            $cliente = Cliente::find($request->idCliente);
            $serie = $request->ptoEmision.$request->facturero;
            $codigoNumerico = 12345678;
            $arrFecha = explode('-',$request->fechaDoc);
            $fecha = $arrFecha[2].$arrFecha[1].$arrFecha[0];

            $request->editar == "true"
                ? $secuencial = substr($request->secuencialEdit,6,9)
                : $secuencial = $usuario->factureros->where('numero',$request->facturero)->first()->n_factura;

            $cadena = $fecha . '01' . $usuario->perfil->ruc . $usuario->perfil->entorno . $serie . (String)$secuencial . $codigoNumerico . 1;
            $digitoVerificador = $this->digitoVerificador($cadena);
            $claveAcceso = $cadena . $digitoVerificador;

            if(!$digitoVerificador && $digitoVerificador!=0){
                return response()->json([
                    'errors'=>[
                        'Mensaje:'=> ' La clave de acceso está mal formada, verifique que su información adicional este correctamente ingresada en la configuración de su perfil y vuelva a intentarlo'
                    ]
                ],500);
            }

            $xml = new DomDocument('1.0', 'UTF-8');
            $factura = $xml->createElement('factura');
            $factura->setAttribute('id', 'comprobante');
            $factura->setAttribute('version', '1.0.0');
            $xml->appendChild($factura);
            $infoTributaria = $xml->createElement('infoTributaria');
            $factura->appendChild($infoTributaria);

            if(Factura::where('clave_acceso',$claveAcceso)->exists() && !$request->editar){
                return response()->json([
                    'errors'=>['Mensaje:'=> 'La clave de acceso ya está registrada']
                ],500);
            }

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
                'secuencial' => $secuencial,
                'dirMatriz' => $usuario->perfil->direc_matriz
            ];

            $request->query->add($informacionTributaria);

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

            $request->query->add($informacionFactura);

            foreach ($informacionFactura as $key => $if) {
                $nodo = $xml->createElement($key, $if);
                $infoFactura->appendChild($nodo);
            }

            $totalConImpuestos = $xml->createElement('totalConImpuestos');
            $infoFactura->appendChild($totalConImpuestos);

            $arrArticulo =[];
            $arrInformacionImpuestos=[];

            foreach ($request->articulos as $articulo) {

                foreach ($articulo['impuestos'] as $impuesto) {

                    $arrInformacionImpuestos[$impuesto['codigo_imp']][$impuesto['codigo_tipo_imp']][]=[
                        'codigo' => $impuesto['codigo_imp'],
                        'codigoPorcentaje' => $impuesto['codigo_tipo_imp'],
                        'baseImponible' => number_format($impuesto['base_imponible'], 2, ".", ""),
                        'valor' => number_format($impuesto['valor_imp'], 2, ".", "")
                    ];
                    
                    $informacionImpuestos = [
                        'codigo' => $impuesto['codigo_imp'],
                        'codigoPorcentaje' => $impuesto['codigo_tipo_imp'],
                        'baseImponible' => number_format($impuesto['base_imponible'], 2, ".", ""),
                        'valor' => number_format($impuesto['valor_imp'], 2, ".", "")
                    ];

                    $arrArticulo['impuesto'][] = $informacionImpuestos;

                }
            }

            $dataTotalImpuestos=[];
            foreach ($arrInformacionImpuestos as $codImp => $arrInformacionImpuesto) {
                foreach ($arrInformacionImpuesto as $codPorc =>  $InformacionImpuesto) {
                    $baseImponible=0;

                    foreach ($InformacionImpuesto as $infoImp) {
                        $baseImponible+=$infoImp['baseImponible'];
                    }

                    $tipoImpuesto = TipoImpuesto::where('codigo',$codPorc)->first();
                    if($tipoImpuesto->tipo_tarifa== '%')
                        $imp = $baseImponible*($tipoImpuesto->tarifa/100);

                    $dataTotalImpuestos[]=[
                        'codigo' => $codImp,
                        'codigoPorcentaje' => $codPorc,
                        'baseImponible' => number_format($baseImponible, 2, ".", ""),
                        'valor' => number_format($imp, 2, ".", "")
                    ];
                }
            }

            foreach ($dataTotalImpuestos as $dataTotalImpuesto) {
                $informacionImpuestos = [
                    'codigo' => $dataTotalImpuesto['codigo'],
                    'codigoPorcentaje' => $dataTotalImpuesto['codigoPorcentaje'],
                    'baseImponible' => number_format($dataTotalImpuesto['baseImponible'], 2, ".", ""),
                    'valor' => number_format($dataTotalImpuesto['valor'], 2, ".", "")
                ];

                $totalImpuesto = $xml->createElement('totalImpuesto');
                $totalConImpuestos->appendChild($totalImpuesto);
                foreach ($informacionImpuestos as $key => $iI) {
                    $nodo = $xml->createElement($key, $iI);
                    $totalImpuesto->appendChild($nodo);
                }
            }

            $request->query->add($arrArticulo);

            $propina = $xml->createElement('propina', '0.00');
            $infoFactura->appendChild($propina);

            $importeTotal = $xml->createElement('importeTotal', number_format($request->total, 2, ".", ""));

            $request->query->add(['importeTotal'=>number_format($request->total, 2, ".", "")]);

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

            $request->query->add(['idFormaPago'=> $request->formaPago]);
            $request->query->add($infoPagos);

            foreach ($infoPagos as $key => $ip) {
                $nodo = $xml->createElement($key, $ip);
                $pago->appendChild($nodo);
            }

            $detalles = $xml->createElement('detalles');
            $factura->appendChild($detalles);

            $articulos = [];
            foreach ($request->articulos as $x => $articulo) {

                $detalle = $xml->createElement('detalle');
                $detalles->appendChild($detalle);
                $informacionDetalle = [
                    'codigoPrincipal' => $articulo['codigo_p'],
                    'descripcion' => $articulo['nombre'],
                    'cantidad' => $articulo['cantidad'],
                    'precioUnitario' => number_format($articulo['neto'], 2, ".", ""),
                    'descuento' => number_format($articulo['descuento'], 2, ".", ""),
                    'precioTotalSinImpuesto' => number_format($articulo['total'], 2, ".", "")
                ];

                foreach ($informacionDetalle as $key => $iD) {
                    $nodo = $xml->createElement($key, $iD);
                    $detalle->appendChild($nodo);
                }

                $informacionDetalle['id_articulo'] = $articulo['id_articulo'];
                $articulos['articulo'][] = $informacionDetalle;

                $impuestos = $xml->createElement('impuestos');
                $detalle->appendChild($impuestos);

                foreach ($articulo['impuestos'] as $imp) {

                    $impuesto = $xml->createElement('impuesto');
                    $impuestos->appendChild($impuesto);
                    $informacionImpuesto = [
                        'codigo' => $imp['codigo_imp'],
                        'codigoPorcentaje' => $imp['codigo_tipo_imp'],
                        'tarifa' => number_format($imp['tarifa'], 2, ".", ""),
                        'baseImponible' => number_format($articulo['total'], 2, ".", ""),
                        'valor' => number_format($imp['valor_imp'], 2, ".", "")
                    ];

                    $articulos['articulo'][$x]['impuestoArticulo'][] = $informacionImpuesto;

                    foreach ($informacionImpuesto as $key => $iIp) {
                        $nodo = $xml->createElement($key, $iIp);
                        $impuesto->appendChild($nodo);
                    }
                }

            }

            $request->query->add($articulos);

            $informacionAdicional = $xml->createElement('infoAdicional');
            $factura->appendChild($informacionAdicional);

            $campos_adicionales = [
                'Dirección'=> $usuario->perfil->direc_establecimiento,
                'Email'    => $usuario->correo,
                'Telefono' => $usuario->tlf
            ];

            $request->query->add($campos_adicionales);

            foreach ($campos_adicionales as $key => $ca) {
                $campoAdicional = $xml->createElement('campoAdicional', $ca);
                $campoAdicional->setAttribute('nombre', $key);
                $informacionAdicional->appendChild($campoAdicional);
            }

            $xml->formatOutput = true;
            $xml->preserveWhiteSpace = false;
            $stringXml = $xml->saveXML();
            $xml = "fact_".$request->ptoEmision.$request->facturero.$secuencial.'.xml';
            $pathFacturas = storage_path('app/public/xml/facturas/');
            $carpetaPersonal = $usuario->id_usuario.'/'.($arrFecha[0].'_'.$arrFecha[1]);
            $request->query->add(['carpeta' => $carpetaPersonal]);

            // GUARDAR DATOS DE LA FACTURA

            $storeFactura = $this->storeFactura($request->all(),true);

            $msg.= $storeFactura['msg'].'<br />';

            if($storeFactura['success']){
                // GUARDAR EL XML
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

                    if($request->firmar =="true"){

                        $resultado = $this->firmarXml($data);
                        $msg .= $this->mensajeFirmaXml($resultado,$data['xml']).'<br />';

                        if ($resultado) {

                            if($request->autorizar == "true"){

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

                                $resultado = $this->enviarXml($data);
                                $factura = Factura::find($storeFactura['id_factura']);

                                if($resultado){

                                    if($resultado[0] == 0){
                                        //ENVIADO PERO RECHAZADO, BUSCAR ARCHIVO EN LA CARPETA RECHAZADOS CORRESPONDIENTE
                                        $causa = $this->comprobanteDevuelta('</factura>',$pathRechazados,$factura->secuencial);
                                        $factura->update([
                                            'estado'=>0,
                                            'causa' => $causa
                                        ]);
                                        $success=false;
                                    }

                                    if($resultado[0] == 1){

                                        //CONSULTAR EL ESTADO DE LA FACTURA
                                        $req = new RequestValidaIdComprobante([
                                            'id_usuario'=> $factura->id_usuario,
                                            'comprobante'=>'factura',
                                            'carpeta_personal'=> $factura->carpeta,
                                            'id_comprobante' => $factura->id_factura,
                                            'clave_acceso' => $factura->clave_acceso
                                        ]);

                                        $response = $this->consultar($req);
                                        $success = $response['success'];
                                        $msg .= $response['msg'];

                                    }

                                    if($resultado[0] == 2){
                                        //FALLA DE CONEXIÓN CON WSDL WEB SERVICE O NO ENVIADO
                                        $factura->update([
                                            'estado'=> $resultado[0],
                                            'causa' => 'Falló la conexión con el sri, por favor intenter reenviar la factura en unos minutos'
                                        ]);
                                        $success=false;
                                    }

                                    $msg .= $this->mensajeEnvioXml($resultado[0]).'<br />';

                                    // FUNCIÓN ENVÍO DE CORREO COMPROBANTE
                                    if($request->correo == "true" && $success){
                                        $data=[
                                            'correos' => $request->correos,
                                            'carpeta_personal' => $carpetaPersonal,
                                            'archivo'=> 'fact_'.$serie.str_pad($secuencial,9, '0', STR_PAD_LEFT),
                                            'usuario' => $usuario->perfil->razon_social
                                        ];
                                        $msg.= $this->envioCorreo($data);
                                    }

                                }else{
                                    return response()->json([
                                        'errors'=>['Mensaje:'=> 'No se pudo enviar el xml generado al sri, busque el registro en la vista de facturas e intente enviarlo nuevamente, si el problema persiste contacte al área de sistemas'],
                                    ],500);
                                }
                            }

                        }else{
                            return response()->json([
                                'errors'=>[
                                    'Mensaje:'=> 'El sistema informa de los siguientes mensajes <br />'. $msg,
                                ]
                            ],500);
                        }

                    }

                }else{
                    return response()->json([
                        'errors'=>['Mensaje:'=> 'No se pudo guardar el xml generado, intente nuevamente, si el problema persiste contacte al área de sistemas'],
                    ],500);
                }

            }else{
                return response()->json([
                    'errors'=>['Mensaje:'=> 'No se pudo guardar el registro de la factura en la base de datos, intente nuevamente'],
                ],500);
            }

            return response()->json([
                'msg' => $msg,
                'success' => $success,
                'factura' => Factura::where('factura.id_factura',$storeFactura['id_factura'])
                                ->join('cliente as c','factura.id_cliente','c.id_cliente')
                                ->select(
                                    'factura.id_factura',
                                    'factura.secuencial',
                                    'factura.clave_acceso',
                                    'factura.total',
                                    'factura.estado',
                                    'factura.correos',
                                    'c.nombre as cliente',
                                    'factura.fecha_doc',
                                    'factura.fecha_aut',
                                    'factura.entorno',
                                    'factura.causa'
                                )->first()
            ],200);

        }catch(\Exception $e){
            return HomeController::catch($e);
        }
    }

    public function storeFactura($request){

        try{

            $fecha = explode('/',$request['fechaEmision']);
            $factura = Factura::updateOrCreate(
                ['id_factura'=> isset($request['id_factura']) ? $request['id_factura'] : 0],
                [
                    'secuencial' => $request['estab'].$request['ptoEmi'].$request['secuencial'],
                    'clave_acceso' => $request['claveAcceso'],
                    'fecha_doc' => $fecha[2].'/'.$fecha[1].'/'.$fecha[0],
                    'entorno' => $request['ambiente'],
                    'id_cliente' => $request['idCliente'],
                    'carpeta' => $request['carpeta'],
                    'id_usuario' => Auth::user()->id_usuario,
                    'fecha_venc' => $request['fechaVenc'],
                    'id_sustento_tributario' => $request['sustTributario'],
                    'total' => $request['total'],
                    'comentario' => isset($request['comentario']) ? $request['comentario'] : '',
                    'correos'=> $request['correos']
                ]
            );

            if(!isset($factura->id_factura))
                $factura = Factura::orderBy('id_factura','desc')->first();

            FacturaPago::create([
                'id_factura' => $factura->id_factura,
                'id_forma_pago' => $request['idFormaPago'],
                'total' => $request['total'],
                'id_tipo_pago' => $request['idTipoPago'],
                'cantidad' => $request['plazo'],
                'und_tiempo' => $request['undTiempoPlazo']
            ]);

            $detalleFactura = DetalleFactura::updateOrCreate(
                ['id_factura' => $factura->id_factura],
                [
                    'id_factura' => $factura->id_factura,
                    'razon_social_emisor' => $request['razonSocial'],
                    'direc_matriz_emisor' => $request['dirMatriz'],
                    'direc_establecimiento_emisor' => $request['dirEstablecimiento'],
                    'obligado_contabilidad' => $request['obligadoContabilidad'],
                    'tipo_ident_comprador' => $request['tipoIdentificacionComprador'],
                    'razon_social_comprador' => $request['razonSocialComprador'],
                    'ident_comprador' => $request['identificacionComprador'],
                    'total_sin_imp' => number_format($request['totalSinImpuestos'],2,".",","),
                    'total_desc' => number_format($request['totalDescuento'],2,".",","),
                    'importe_total' => number_format($request['importeTotal'],2,".",",")
                    //'propina' => $request['propina'],
                ]
            );

            if(!isset($detalleFactura->id_detalle_factura))
                $detalleFactura = DetalleFactura::orderBy('id_detalle_factura','desc')->first();

            $oldImpuestoDetalleFactura = ImpuestoDetalleFactura::where('id_detalle_factura',$detalleFactura->id_detalle_factura)
                                            ->pluck('id_impuesto_detalle_factura')->toArray();

            $oldArticulosFactura = ArticuloFactura::where('id_factura',$factura->id_factura)
                                            ->pluck('id_articulo_factura')->toArray();

            foreach ($request['articulo'] as $x => $articulo) {

                $articuloCategoriaInventario = ArticuloCategoriaInventario::where('id_articulo_categoria_inventario',$articulo['id_articulo'])->first();

                ArticuloFactura::create([
                    'id_factura' =>  $factura->id_factura,
                    'codigo_p' => $articulo['codigoPrincipal'],
                    'codigo_a' =>  '',
                    'cantidad' => $articulo['cantidad'],
                    'precio_unitario' => $articulo['precioUnitario'],
                    'descuento' => $articulo['descuento'],
                    'precio_total_sin_imp' => $articulo['precioTotalSinImpuesto'],
                    'descripcion' => $articulo['descripcion'],
                    'id_categoria_inventario' => $articuloCategoriaInventario->id_categoria_inventario,
                    'id_articulo_categoria_inventario' => $articulo['id_articulo']
                ]);

                $art = ArticuloFactura::all()->last();

                foreach($articulo['impuestoArticulo'] as $impuestoArticulo){

                    ImpuestoArticuloFactura::create([
                        'id_articulo_factura' => $art->id_articulo_factura,
                        'codigo_impuesto' => $impuestoArticulo['codigo'],
                        'codigo_porcentaje' => $impuestoArticulo['codigoPorcentaje'],
                        'base_imponible' => $impuestoArticulo['baseImponible'],
                        'valor' => $impuestoArticulo['valor']
                    ]);

                }

            }

            if(($x+1) == count($request['articulo'])){

                foreach ($request['impuesto'] as $y => $impuesto) {
                    ImpuestoDetalleFactura::create([
                        'id_detalle_factura' => $detalleFactura->id_detalle_factura,
                        'codigo_impuesto' => $impuesto['codigo'],
                        'codigo_porcentaje' => $impuesto['codigoPorcentaje'],
                        'base_imponible' => $impuesto['baseImponible'],
                        'valor' => $impuesto['valor']
                    ]);
                }

                if(($y+1) == count($request['impuesto']))
                    ImpuestoDetalleFactura::destroy($oldImpuestoDetalleFactura);
            }

            if((($x+1) == count($request['articulo'])))
                ArticuloFactura::destroy($oldArticulosFactura);

            //ACTUALIZAR EL SECUENCIAL
            if($request['editar']=="false"){
                $usuario = Auth::user();
                $facturero = $usuario->factureros->where('numero',$request['ptoEmi'])->first();
                $facturero = UsuarioFacturero::find($facturero->id_usuario_facturero);
                $facturero->update(['n_factura'=> str_pad($request['secuencial']+1, 9, '0', STR_PAD_LEFT)]);
            }

            return [
                'msg' => 'El xml N° '.$factura->secuencial.' se ha guardado y registrado',
                'success' => true,
                'id_factura'=>$factura->id_factura
            ];

        }catch(\Exception $e){
            if(isset($factura))
                Factura::destroy($factura->id_factura);

            return HomeController::catch($e);
        }

    }

    public function pdfFactura($idFactura){

        $factura = Factura::where([
            'id_usuario' => Auth::user()->id_usuario,
            'id_factura' => $idFactura
        ])->first();

        $pdf = storage_path('app/public/pdf/facturas/'.$factura->carpeta.'/'.'fact_'.$factura->secuencial.'.pdf');

        if(!file_exists($pdf)){
            try{
                $response =  $this->autorizacionComprobante($factura->clave_acceso, $factura->id_usuario);
                $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;
                PdfFactura::dispatchNow($factura->carpeta,$autorizacion,$factura->usuario->perfil->logo_empresa);
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

        return response()->file($pdf);
    }

    public function anular(Request $request){

        try{
            $factura = Factura::find($request->id_factura);
            $factura->update([
                'estado' => 3,
                'causa' => 'Factura anulada manualmente'
            ]);


            $data=[
                'carpeta_personal' => $factura->carpeta,
                'correos' => $factura->correos,
                'archivo' =>'fact_'.$factura->secuencial,
                'usuario' => $factura->usuario->perfil->razon_social,
                'anulacion' => true
            ];

            $msg = $this->envioCorreo($data);

            return response()->json([
                'msg' => 'Se ha anulado la factura N° '.$factura->secuencial.' <br />'.  $msg,
                'estado' => $factura->estado
            ]);



        }catch (\Exception $e){
            return HomeController::catch($e);
        }
    }

    public function reenviarCorreo(RequestReenvioCorreoComprobante $request){

        try{

            $data=[
                'carpeta_personal' => $request->carpeta,
                'correos' => $request->correos,
                'archivo' =>'fact_'.$request->secuencial,
                'usuario' => $request->razon_social
            ];

            $this->envioCorreo($data);

            return response()->json([
                'msg'=> 'Se ha enviado el correo con la factura y xml '.'fact_'.$request->secuencial.'  a los siguientes destinatarios: <br /> '.trim($request->correos).''
            ]);

        }catch(\Execption $e){
            return HomeController::catch($e);
        }

    }

    public function editar(RequestValidaIdComprobante $request){

        try{

            $factura = Factura::find($request->id_comprobante);

            return response()->json([
                'ptoEmi' => substr($factura->clave_acceso, 24, 3),
                'facturero' => substr($factura->clave_acceso, 27, 3),
                'secuencial' => $factura->secuencial,
                'fechaDoc' => $factura->fecha_doc,
                'fechaVenc' => $factura->fecha_venc,
                'sustTributario' => $factura->id_sustento_tributario,
                'idCliente' => $factura->id_cliente,
                'comentario' => $factura->comentario,
                'correos'=> $factura->correos,
                'articulos' => $factura->articulos,
                'formaPago'=>$factura->pagos[0]->id_forma_pago,
                'idTipoPago'=> $factura->pagos[0]->id_tipo_pago,
                'plazo' => $factura->pagos[0]->cantidad,
                'unTiempoPlazo' => $factura->pagos[0]->und_tiempo
            ]);

        }catch(\Exception $e){
            return HomeController::catch($e);
        }
    }

    public function consultar(RequestValidaIdComprobante $request){

        $usuario = Usuario::find($request->id_usuario);

        $logo = $usuario->perfil->logo_empresa;
        $data = $this->consultarComprobante($request);

        if($data['estado'] !=2){
            if($data['estado']==1)
                PdfFactura::dispatch($request->carpeta_personal,$data['autorizacion'],$logo)->onQueue('pdf_factura');
            if($data['estado']!=4 && $data['estado']!=3)
                XmlRespuestaFactura::dispatch($request->carpeta_personal,$usuario->id_usuario,$data['autorizacion'])->onQueue('respuesta_sri_factura');
        }

        return [
            'success' => $data['success'],
            'msg' => $data['msg'],
            'estado' => $data['estado']
        ];

    }

}
