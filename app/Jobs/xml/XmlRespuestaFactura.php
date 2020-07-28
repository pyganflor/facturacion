<?php

namespace App\Jobs\xml;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Model\Factura;
use Illuminate\Support\Facades\Storage;
use DOMDocument;
use SimpleXMLElement;


class XmlRespuestaFactura implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 5;
    protected $carpetaPersonal;
    protected $idUsuario;
    protected $autorizacion;

    public function __construct($carpetaPersonal,$idUsuario,$autorizacion)
    {
        $this->carpetaPersonal = $carpetaPersonal;
        $this->idUsuario = $idUsuario;
        $this->autorizacion = $autorizacion;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fechaAutorizacion = (String)$this->autorizacion->fechaAutorizacion;
        $ambiente = (String)$this->autorizacion->ambiente;
        $dataXML = (String)$this->autorizacion->comprobante;
        $xml = new SimpleXMLElement($dataXML);
        $secuencial = substr((String)$xml->infoTributaria->claveAcceso, 24, 15);
        $xml = new DOMDocument(1.0, 'UTF-8');
        $xml->loadXML($dataXML);

        $nuevoXml = new DOMDocument(1.0, 'UTF-8');
        $nuevoXml->formatOutput = true;

        if ($this->autorizacion->estado === "AUTORIZADO") {
            $nuevoXml->loadXML("<autorizacion><estado>" . $this->autorizacion->estado . "</estado><ambiente>" . $ambiente . "</ambiente><fechaAutorizacion>" . $fechaAutorizacion . "</fechaAutorizacion><numeroAutorizacion>" . (String)$this->autorizacion->numeroAutorizacion . "</numeroAutorizacion></autorizacion>");
            $disk = Storage::disk('xml_autorizado');
        } else {
            $disk = Storage::disk('xml_no_autorizado');
            $causa = "";
            foreach ($this->autorizacion->mensajes as $mensaje)
                $causa .= $mensaje->mensaje . ": " . $mensaje->informacionAdicional . ", Tipo: " . $mensaje->tipo . ", ";

            $nuevoXml->loadXML("<autorizacion><estado>" . $this->autorizacion->estado . "</estado><ambiente>" . $ambiente . "</ambiente><fechaAutorizacion>" . $this->autorizacion->fechaAutorizacion . "</fechaAutorizacion><numeroAutorizacion>" . $this->autorizacion->estado . "</numeroAutorizacion><causa>" . $causa . "</causa></autorizacion>");

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
        $disk->put($this->carpetaPersonal.'/'.$xml, $stringXml);

    }
}
