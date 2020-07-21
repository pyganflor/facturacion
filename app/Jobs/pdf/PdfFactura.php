<?php

namespace App\Jobs\pdf;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SimpleXMLElement;
use SoapClient;

class PdfFactura implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $response;
    public $capertaPersonal;

    public function __construct($capertaPersonal,$response)
    {
        $this->carpeta = $capertaPersonal;
        $this->response = $response;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        if($this->response && isset($this->response->comprobante)){

            $xml = new SimpleXMLElement((String)$this->response->comprobante);

            $data=[
                'nombre_comercial' => (String)$xml->infoTributaria->nombreComercial,
                'dir_matriz' => (String)$xml->infoTributaria->dirMatriz,
                'dir_establecimiento' => (String)$xml->infoFactura->dirEstablecimiento,
                'obligado_contabilidad' => (String)$xml->infoFactura->obligadoContabilidad,
                'ruc' => (String)$xml->infoTributaria->ruc,
                'secuencial' => (String)$xml->infoTributaria->estab.(String)$xml->infoTributaria->codDoc.(String)$xml->infoTributaria->secuencial,
                'clave_acceso' => (String)$xml->infoTributaria->claveAcceso,
                'ambiente' => (String)$xml->infoTributaria->ambiente == 1 ? 'PRUEBAS' : 'PRODUCCIÓN',
                'tipo_emision' => (String)$this->response->ambiente == 1 ? 'NORMAL' : 'CONTINGENCIA',
                'razon_social_comprador' => (String)$xml->infoFactura->razonSocialComprador,
                'identificacion_comprador' => (String)$xml->infoFactura->identificacionComprador,
                'fecha_emision' => (String)$xml->infoFactura->fechaEmision,
                'fecha_autorizacion' => (String)$this->response->fechaAutorizacion,
                'direccion' => (String)$xml->infoAdicional->campoAdicional[0],
                'correo' => (String)$xml->infoAdicional->campoAdicional[1],
                'telefono' => ''
            ];

        }else{

        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('comprobantes.factura.pdf', compact('data'));
        $pdf->save(storage_path('app/public/pdf/facturas/'). 'fact_'.$data['secuencial'].'.pdf');
    }
}
