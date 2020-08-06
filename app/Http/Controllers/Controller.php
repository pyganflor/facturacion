<?php

namespace App\Http\Controllers;

use App\Model\Factura;
use App\Model\Usuario;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use DOMDocument;
use SimpleXMLElement;
use App\Mail\EnvioComprobante;
use Illuminate\Http\Request;
use App\Http\Requests\RequestValidaIdComprobante;
use stdClass;
use SoapClient;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param $cadena
     * @return bool|float|int
     * @action Genera el dígito verificador con la fórmula del módulo 11
     */
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
                if ($x == 8) $x = 2;
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

    /**
     * @param $data
     * @return bool
     * @action Firma un documento XML con la firma digital
     */
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
            0 => "El comprobante fue enviado pero rechazado por el SRI, verfique en el estado rechazado, en la columna mensaje el motivo",
            1 => "Se ha autorizado con éxito la factura por el sri",
            2 => "Falló en la conexión con el web service del SRI, intente nuevamente"
        ];
        if(!isset($mensaje[$indice])){
            return $indice;
        }else{
            return $mensaje[$indice];
        }

    }

    /**
     * @param $data
     * @return string
     * @action acciona el queue que envia el correo al cliente con los comprobantes electrónicos
     */
    function envioCorreo($data){

        $correos = explode(',',$data['correos']);
        $correosValidos = [];
        $correosNoValidos=[];
        $data['anulacion'] = isset($data['anulacion']) ? $data['anulacion'] : false;

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

            $message = (new EnvioComprobante($data['carpeta_personal'],$data['archivo'],$data['usuario'],$data['anulacion']))->onQueue('emails');

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

    /**
     * @param $tagComprobante
     * @param $pathRechazados
     * @param $secuencial
     * @action Acción que se ejecuta cuando el sri devuelve por x motivo el xml y no lo aprueba
     * @return string
     */
    function comprobanteDevuelta($tagComprobante, $pathRechazados, $secuencial){

        $xmlDevuelta = new DOMDocument(1.0, 'UTF-8');
        $xmlDevuelta->load($pathRechazados.'fact_'.$secuencial.'.xml');
        $nodo = $xmlDevuelta->saveXML();
        $a = explode($tagComprobante,explode('</ds:Signature>',$nodo)[1])[0];
        $xml2 = new DOMDocument(1.0, 'UTF-8');
        $xml2->loadXML($a);
        $b = $xml2->saveXML();
        $c = new SimpleXMLElement($b);
        $msg='';
        foreach ($c->comprobantes as $comprobante) {
            foreach ($comprobante->comprobante->mensajes as $mensaje){
                $msg.= (String)$mensaje->mensaje->mensaje.':';
                $msg.= (String)$mensaje->mensaje->informacionAdicional.'. ';
            }
        }
        return $msg;
    }

    function consultarComprobante(RequestValidaIdComprobante $request){

        $msg='';
        $estado = 1;
        $success=false;
        $autorizacion='';
        try{

            switch ($request->comprobante){
                case 'factura':
                    $comprobante = Factura::find($request->id_comprobante);
                    break;
            }

            $response = $this->autorizacionComprobante($request->clave_acceso,$request->id_usuario);

            if($response->RespuestaAutorizacionComprobante->numeroComprobantes>0){
                $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;

                if($autorizacion->estado=="NO AUTORIZADO" || $autorizacion->estado=="RECHAZADA") {
                    $estado = 0;
                    $causa = "Comprobante rechazado o no autorizado";
                }else{
                    $success=true;
                    $causa="Comprobante autorizado por el SRI,<br /> 
                                En fecha y hora: ".Carbon::parse($autorizacion->fechaAutorizacion)->format('d-m-Y H:i:s')."<br />";
                }

                $msg .= $causa;
                $comprobante->update([
                    'estado'=> $estado,
                    'fecha_aut' =>(String)$autorizacion->fechaAutorizacion,
                    'causa' => $causa
                ]);

            }else{
                $msg.='El comprobante no fue recibido por el SRI, por favor intente reenviarlo nuevamente';
                $estado=2;
                $comprobante->update([
                    'estado'=> $estado,
                    'causa' => $msg
                ]);
            }

        }catch(\Exception $e){
            $success=false;
            $estado= 4;
            $msg.='El web service del SRI no está disponible en este momento, por favor intente consultarlo nuevamente en unos minutos <br />'.$e->getMessage().'';
            $comprobante->update([
                'estado'=> $estado,
                'causa' => $msg
            ]);
        }

        return [
            'success' => $success,
            'msg' => $msg,
            'estado' => $estado,
            'autorizacion' => $autorizacion
        ];
    }

    /**
     * @param $claveAcceso
     * @param null $idUsuario
     * @return mixed
     * @action Verifica una clave de acceso en el ws del SRI
     */
    function autorizacionComprobante($claveAcceso, $idUsuario=null){
        $usuario = isset($idUsuario) ? Usuario::find($idUsuario) : Auth::user();
        $wsdlAutorizacion ='https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';
        //$wsdlAutorizacion = $usuario->perfil->entorno == 1 ? env('WSDL_PRUEBAS_AUTORIZACION') : env('WSDL_PRODUCCION_AUTORIZACION');
        $clienteSoap = new SoapClient($wsdlAutorizacion);
        return $clienteSoap->autorizacionComprobante(["claveAccesoComprobante" => $claveAcceso]);
    }
}
