<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaFirmaElectronica as AlertFirma;

class AlertaFirmaElectronica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerta:firma_electronica';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron que se ejecuta una vez al día para verificar el tiempo de duración de las firmas electrónicas de los clientes y enviar un correo si estan por vencerse';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $usuarios = Usuario::join('usuario_perfil as up','usuario.id_usuario','up.id_usuario')
            ->where('usuario.estado',true)->whereNotNull('up.firma_elec')
            ->whereNotNull('up.firma_hasta')
            ->select('up.razon_social','up.firma_hasta','usuario.correo')->get();

        $hoy = now();

        foreach ($usuarios as $usuario) {

            $diasParaVencerse = Carbon::parse($usuario->firma_hasta)->diffInDays($hoy);
            $data=[
                'razon_social' => $usuario->razon_social,
                'firma_hasta' => $usuario->firma_hasta,
                'correo' => $usuario->correo
             ];

            if($diasParaVencerse <= 15)
                Mail::to($usuario->correo)->queue((new AlertFirma($data))->onQueue('emails_firma_electronica'));

        }
    }
}
