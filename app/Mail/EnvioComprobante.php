<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class EnvioComprobante extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $carpetaPersonal;
    public $nombreArchivo;
    public $usuario;
    public $anulacion;


    public function __construct($carpetaPersonal,$nombreArchivo,$usuario,$anulacion)
    {
        $this->carpetaPersonal = $carpetaPersonal;
        $this->nombreArchivo = $nombreArchivo;
        $this->usuario = $usuario;
        $this->anulacion = $anulacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = storage_path('app/public/pdf/facturas/'.$this->carpetaPersonal.'/'.$this->nombreArchivo.'.pdf');
        $xml = storage_path('app/public/xml/facturas/autorizado/'.$this->carpetaPersonal.'/'.$this->nombreArchivo.'.xml');

        $mail= $this->from("facturacion_electronica@pyganflor.com")
            ->subject($this->usuario . ($this->anulacion ? ' Anulación':''). ', comprobante electrónico N° '.$this->nombreArchivo)
            ->view('comprobantes.factura.correo_comprobante')->with([
                'usuario',$this->usuario,
                'anulacion',$this->anulacion
            ]);

        if(file_exists($xml))
            $mail->attach($xml,[
                'as' => $this->nombreArchivo.'.xml'
            ]);

        if(file_exists($pdf))
            $mail->attach($pdf,[
                'as' =>$this->nombreArchivo.'.pdf'
            ]);
    }
}
