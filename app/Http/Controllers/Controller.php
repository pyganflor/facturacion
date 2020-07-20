<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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
            1 => "Verificar lo explicado en el Índice 0 de este apartado y a su vez verificar que exista el certificado como archivo físico, una vez corregido el error puede filtrar por 'NO FIRMADOS' y proceder a realizar la firma del mismo",
            2 => "No se pudo acceder al contenido del archivo del certificado electrónico, verifique los indicies 0 y 1 de este apartado  y a su vez que el String pasado en la variable 'CONTRASENA_FIRMA_DIGITAL' en el archivo .env coincida con la propocionada por el ente certificador, una vez corregido el error puede filtrar por 'NO FIRMADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
            3 => "Se produjo un error al momento de generar la firma electrónica del xml " . $archivo . ", por favor comunicarse con el deparatmento de tecnología, una vez corregido el error puede filtrar por 'NO FIRMADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
            4 => "El archivo firmado xml N# " . $archivo . " no pudo ser guardado en su respectiva carpeta, verifique que el path propocionado en la variable de entorno 'PATH_XML_FIRMADOS' en el archivo .env coincida con la carpeta creada en esa ruta, una vez corregido el error puede filtrar por 'GENERADOS' en la vista de comprobantes y proceder a realizar la firma del mismo",
            5 => "El comprobante N# " . $archivo . " se ha generado y firmado con éxito",
        ];
        return $mensaje[$indice];
    }

    function mensajeEnvioXml($indice)
    {
        $mensaje = [
            0 => "El comprobante fue enviado pero rechazado por el SRI, verfique en el campo causa el motivo",
            1 => "Se ha autorizado con éxito la factura por el sri",
            2 => "Fallo en la conexión con el web service del SRI, intente nuevamente"
        ];
        return $mensaje[$indice];
    }

    function envioCorreo($data){

    }
}
