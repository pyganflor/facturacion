<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnvioComprobante;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function digitoVerificador($cadena)
    {
        $arrNum = str_split($cadena);
        $cantCadena = count($arrNum);

        $total = 0.00;
        if ($cantCadena === 48) {
            $x = 2;
            for ($i = 47; $i >= 0; $i--) {
                $cantidad = $arrNum[$i] * $x;
                $total += $cantidad;
                $x++;
                if ($x == 8)
                    $x = 2;
            }
            $cociente = $total / 11;
            $producto = ((int)$cociente) * 11;
            $resultado = $total - $producto;
            $digitoVerificador = 11 - $resultado;

            if ((11 * (int)$cociente) + $resultado === $total) {


                if ($digitoVerificador == 10)
                    $digitoVerificador = 1;
                elseif ($digitoVerificador == 11)
                    $digitoVerificador = 0;

                return $digitoVerificador;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    function firmarXml($data)
    {
        exec("java -Dfile.encoding=UTF-8 -jar " . env('PATH_FIRMADOR') . " "
            . $data['carpetaGenerados'] . " "
            . $data['xml'] . " "
            . $data['carpetaFirmardos']. " "
            . $data['archivoFirmaDigital'] . " "
            . $data['contrasena'] . " "
            . $data['nombreArchivoFirmaDigital'] . " ",
            $salida, $var);

        if ($var == 0)
            return $salida[0];
        if ($var != 0)
            return false;

    }

    function enviarXml($data)
    {
        ini_set('max_execution_time', 600);
        set_time_limit (600);
        exec('java -Dfile.encoding=UTF-8 -jar ' . env('PATH_ENVIADOR') . " "
            . $data['carpetaFirmardos'] . " "
            . $data['xml']. " "
            . $data['carpetaEnviados'] . " "
            . $data['carpetaRechazados'] . " "
            . $data['carpetaAutorizados']  . " "
            . $data['carpetaNoAutorizados'] . " "
            . $data['wsdlRecepcion'] . " "
            . $data['wsdlAutorizacion'] . " "
            . $data['claveAcceso'] . " ",
            $salida, $var);

        if ($var == 0)
            return $salida;
        if ($var != 0)
            return false;
    }

    function mensajeFirmaXml($indice, $archivo)
    {
        $mensaje = [
            0 => "No se ha obtenido el archivo de la firma digital correctamente, verifique que el archivo este debidamente cargado en el sistema, una vez corregido el error puede filtrar por 'NO FIRMADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
            1 => "No se pudo firma el archivo, verifique que halla cargado su firma electrónica en sus configuraciones de perfil",
            2 => "No se pudo acceder al contenido del archivo del archivo de certificado electrónico,verifique que la contraseña ingresada en su configuraciones de perfil sea la correcta",
            3 => "Se produjo un error al momento de generar la firma electrónica del xml " . $archivo . ", por favor comunicarse con el administrador del sistema",
            4 => "El archivo firmado xml N# " . $archivo . " no pudo ser guardado en su respectiva carpeta",
            5 => "El comprobante N# " . $archivo . " se firmó con éxito",
        ];
        return $mensaje[$indice];
    }

    function mensajeEnvioXml($indice)
    {
        $mensaje = [
            0 => "El comprobante fue enviado pero rechazado por el SRI, verfique en el estado rechazado, en la columna causa el motivo",
            1 => "Se ha autorizado con éxito la factura por el sri",
            2 => "Fallo en la conexión con el web service del SRI, intente nuevamente"
        ];
        return $mensaje[$indice];
    }

    function envioCorreo($data){

        $correos = explode(',',$data['correos']);
        $correosValidos = [];
        $correosNoValidos=[];

        foreach ($correos as $correo) {
            if(trim($correo)!=""){
                $valid = preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',trim($correo));
                if($valid==0){
                    $correosNoValidos[] = trim($correo);
                }else{
                    $correosValidos[] = trim($correo);
                }
            }
        }

        if(count($correosValidos)>0){

            $message = (new EnvioComprobante($data['carpeta_personal'],$data['archivo'],$data['usuario']))->onQueue('emails');

            $msg='';
            $mail = Mail::to($correosValidos[0]);

            if(count($correosValidos)>=2)
                $mail->cc(array_slice($correosValidos,1));

            $mail->queue($message);

            $enviado='';
            foreach ($correosValidos as $correoValido)
                $enviado.= '<br />'.$correoValido;

            $noEnviado='';
            foreach ($correosNoValidos as $correoNoValido)
                $noEnviado.= '<br />'.$correoNoValido;

            $msg.='Se envió el correo a los siguientes destinatarios:'.$enviado;

            if(count($correosNoValidos)>0)
                $msg.= '<br /> No se enviaron los correos a las siguientes direcciones ya que eran no válidas:'.$noEnviado;
        }else{
            $msg = 'No se envió el correo electrónico ya que no hubo destinatarios a los que envíar';
        }

        return $msg;

    }
}
