<?php

use Illuminate\Support\Facades\Route;

/*Route::get('prueba_sri',function(){
    $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';
    $client = new SoapClient($url);
    $directorio = storage_path('app/public/xml/facturas/firmado/1/2020_08/fact_001777000000102.xml');
    $xml = file_get_contents($directorio);
    $parametros = new stdClass();
    $parametros->xml = $xml;
    $result = $client->validarComprobante($parametros);
    var_dump($result);
});


Route::get('retencion',function(){

    $clienteSoap = new SoapClient(env('WSDL_PRODUCCION_AUTORIZACION'));
    $response = $clienteSoap->autorizacionComprobante(["claveAccesoComprobante" => '0307202007179218627700120010030000047220000142510']);
    $autorizacion = $response->RespuestaAutorizacionComprobante;

    dd($autorizacion);
});*/

Route::get('/', 'HomeController@index');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

//USUARIOS LOGUEADOS
Route::group(['middleware' => 'auth'],function () {

    include_once 'usuarios/perfil/rutas.php';
    include_once 'usuarios/clientes/rutas.php';
    include_once 'usuarios/inventario/rutas.php';
    include_once 'usuarios/proveedores/rutas.php';
    include_once 'usuarios/comprobantes/factura.php';
    include_once 'usuarios/comprobantes/retenciones_cliente.php';
    include_once 'usuarios/comprobantes/facturas_cliente.php';

    Route::group(['middleware'=>'Permiso'],function (){

        //Administrador
        include_once 'super_administrador/usuarios/rutas.php';
        include_once 'super_administrador/modulos/rutas.php';
    });



});
