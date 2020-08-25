<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaFirmaElectronica extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

       $this->from("facturacion_electronica@pyganflor.com")
            ->subject('Notificación de vencimiento de firma electrónica')
            ->view('usuarios.mail_alerta_firma_electronica')->with([
                'usuario'=>$this->data
            ]);
    }
}
