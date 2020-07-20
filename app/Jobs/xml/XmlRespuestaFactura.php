<?php

namespace App\Jobs\xml;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Model\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use SoapClient;
use DOMDocument;
use Illuminate\Support\Facades\Auth;


class XmlRespuestaFactura implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $claveAcceso;
    protected $idUsuario;

    public function __construct($claveAcceso,$idUsuario)
    {
        $this->claveAcceso = $claveAcceso;
        $this->idUsuario = $idUsuario;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $entorno = substr($this->claveAcceso, 23, 1);
        $wsdlAutorizacion = $entorno == 1  ? env('WSDL_PRUEBAS_AUTORIZACION') : env('WSDL_PRODUCCION_AUTORIZACION');
        $secuencial = substr($this->claveAcceso, 24, 15);
        $cliente = new SoapClient($wsdlAutorizacion);
        $response = $cliente->autorizacionComprobante(["claveAccesoComprobante" => $this->claveAcceso]);
        $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;
        $fechaAutorizacion = (String)$autorizacion->fechaAutorizacion;
        $ambiente = (String)$autorizacion->ambiente;
        $dataXML = (String)$autorizacion->comprobante;

        $xml = new DOMDocument(1.0, 'UTF-8');
        $xml->loadXML($dataXML);
        $nodo = $xml->getElementsByTagName("factura")->item(0);

        $nuevoXml = new DOMDocument(1.0, 'UTF-8');
        $nuevoXml->formatOutput = true;

        if ($autorizacion->estado === "AUTORIZADO") {
            $nuevoXml->loadXML("<autorizacion><estado>" . $autorizacion->estado . "</estado><ambiente>" . $ambiente . "</ambiente><fechaAutorizacion>" . $fechaAutorizacion . "</fechaAutorizacion><numeroAutorizacion>" . (String)$autorizacion->numeroAutorizacion . "</numeroAutorizacion></autorizacion>");
            $disk = Storage::disk('xml_autorizado');
        } else {
            $disk = Storage::disk('xml_no_autorizado');
            $causa = "";
            foreach ($autorizacion->mensajes as $mensaje)
                $causa .= $mensaje->mensaje . ": " . $mensaje->informacionAdicional . ", Tipo: " . $mensaje->tipo . ", ";

            $nuevoXml->loadXML("<autorizacion><estado>" . $autorizacion->estado . "</estado><ambiente>" . $ambiente . "</ambiente><fechaAutorizacion>" . $autorizacion->fechaAutorizacion . "</fechaAutorizacion><numeroAutorizacion>" . $autorizacion->estado . "</numeroAutorizacion><causa>" . $causa . "</causa></autorizacion>");

            Factura::where([
                ['id_usuario', $this->idUsuario],
                ['secuencial', $secuencial]
            ])->update(['causa' => $causa]);
        }

        $comprobante= $nuevoXml->createElement('comprobante');
        $nodo = $xml->saveXML();
        $comprobante->appendChild($nuevoXml->createCDATASection($nodo));
        $nodeAutorizacion =  $nuevoXml->getElementsByTagName("autorizacion")->item(0);
        $nodeAutorizacion->appendChild($comprobante);

        $stringXml = $nuevoXml->saveXML();
        $xml = "fact_".$secuencial.'.xml';
        $carpetaPersonal = $this->idUsuario.'/'.Carbon::now()->format('Y_m').'/';
        $disk->put($carpetaPersonal.$xml, $stringXml);



    }
}
