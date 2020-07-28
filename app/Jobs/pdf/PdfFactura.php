<?php

namespace App\Jobs\pdf;

use App\Model\TipoPago;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SimpleXMLElement;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class PdfFactura implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;
    public $carpeta;
    public $response;
    public $logo;

    public function __construct($carpeta,$response,$logo)
    {
        $this->response = $response;
        $this->logo = $logo;
        $this->carpeta = $carpeta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $barcode = new BarcodeGenerator();
        $xml = new SimpleXMLElement((String)$this->response->comprobante);
        $articulos= [];
        $subtotal12=0;
        $subtotal14=0;
        $subtotal0=0;
        $excento =0;
        $noObjeto =0;
        $iva12 = 0;
        $iva14=0;

        $barcode->setText((String)$this->response->numeroAutorizacion);
        $barcode->setType(BarcodeGenerator::Code128);

        foreach ($xml->detalles->detalle as $detalle) {

            $articulos[]=[
                'cod_p' => (String)$detalle->codigoPrincipal,
                'descripcion' =>(String)$detalle->descripcion,
                'cantidad' =>(String)$detalle->cantidad,
                'p_unitario' => (String)$detalle->precioUnitario,
                'descuento' => (String)$detalle->descuento,
                'total' => (String)$detalle->precioTotalSinImpuesto
            ];

            foreach ($detalle->impuestos->impuesto as $impuesto) {

                if((String)$impuesto->codigoPorcentaje==2){

                    $subtotal12+=(float)$impuesto->baseImponible;
                    $iva12+= (float)$impuesto->valor;

                }else if((String)$impuesto->codigoPorcentaje==3){

                    $subtotal14+=(float)$impuesto->baseImponible;
                    $iva14 += (float)$impuesto->valor;

                }else if((String)$impuesto->codigoPorcentaje==0){

                    $subtotal0+=(float)$impuesto->baseImponible;

                }else if((String)$impuesto->codigoPorcentaje==7){

                    $excento+=(float)$impuesto->baseImponible;

                }else if((String)$impuesto->codigoPorcentaje==6){

                    $noObjeto+=(float)$impuesto->baseImponible;
                }
            }

        }

        $pagos=[];
        foreach ($xml->infoFactura->pagos as $pago) {
            $pagos[]=[
                'tipo_pago' => TipoPago::where('codigo',(String)$pago->pago->formaPago)->first()->nombre,
                'total' => (String)$pago->pago->total,
                'tiempo' => (String)$pago->pago->plazo.' '.(String)$pago->pago->unidadTiempo
            ];
        }

        $data=[
            'nombre_comercial' => (String)$xml->infoTributaria->nombreComercial,
            'dir_matriz' => (String)$xml->infoTributaria->dirMatriz,
            'dir_establecimiento' => (String)$xml->infoFactura->dirEstablecimiento,
            'obligado_contabilidad' => (String)$xml->infoFactura->obligadoContabilidad,
            'ruc' => (String)$xml->infoTributaria->ruc,
            'secuencial' => (String)$xml->infoTributaria->estab.(String)$xml->infoTributaria->ptoEmi.(String)$xml->infoTributaria->secuencial,
            'clave_acceso' => (String)$xml->infoTributaria->claveAcceso,
            'ambiente' => (String)$xml->infoTributaria->ambiente == 1 ? 'PRUEBAS' : 'PRODUCCIÓN',
            'tipo_emision' => (String)$xml->infoTributaria->tipoEmision == 1 ? 'NORMAL' : 'CONTINGENCIA',
            'razon_social_comprador' => (String)$xml->infoFactura->razonSocialComprador,
            'identificacion_comprador' => (String)$xml->infoFactura->identificacionComprador,
            'fecha_emision' => (String)$xml->infoFactura->fechaEmision,
            'fecha_autorizacion' => Carbon::parse((String)$this->response->fechaAutorizacion)->format('d/m/Y H:m:i'),
            'total_descuento' => number_format((float)$xml->infoFactura->totalDescuento,2,".",","),
            'artiuclos' => $articulos,
            'pagos' => $pagos,
            'direccion' => (String)$xml->infoAdicional->campoAdicional[0],
            'correo' => (String)$xml->infoAdicional->campoAdicional[1],
            'telefono' => '',
            'subtotal_12' => number_format($subtotal12,2,".",","),
            'subtotal_14' => number_format($subtotal14,2,".",","),
            'subtotal_0' => number_format($subtotal0,2,".",","),
            'subtotal_no_objeto' => number_format($noObjeto,2,".",","),
            'subtotal_sin_imp' =>'0.00',
            'subtotal_exento' =>number_format($excento,2,".",","),
            'ice' =>'0.00',
            'iva_12' =>number_format($iva12,2,".",","),
            'iva_14' =>number_format($iva14,2,".",","),
            'IRBPNR' => '0.00',
            'propina' =>'0.00',
            'total' => number_format((String)$xml->infoFactura->importeTotal,2,".",","),
            'bar_code' => $barcode->generate(),
            'img_usuario' => $this->logo
        ];

        $ruta= storage_path('app/public/pdf/facturas/').$this->carpeta;

        if(!file_exists($ruta))
            mkdir($ruta, 0775, true);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('comprobantes.factura.pdf', compact('data'));
        $pdf->save($ruta.'/fact_'.$data['secuencial'].'.pdf');
    }
}
