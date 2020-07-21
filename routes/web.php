<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('pdf',function(){

    $clienteSoap = new SoapClient(env('WSDL_PRUEBAS_AUTORIZACION'));
    $response = $clienteSoap->autorizacionComprobante(["claveAccesoComprobante" => '2107202001179244632500110017770000000621234567815']);
    $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;

    $carpetaPersonal ='1/2020_07';
    \App\Jobs\pdf\PdfFactura::dispatch($carpetaPersonal,$autorizacion)->onQueue('pdf_factura');
});

//USUARIOS LOGUEADOS
Route::group(['middleware' => 'auth'],function () {

    include_once 'usuarios/perfil/rutas.php';
    include_once 'usuarios/clientes/rutas.php';
    include_once 'usuarios/inventario/rutas.php';
    include_once 'usuarios/proveedores/rutas.php';
    include_once 'usuarios/comprobantes/factura.php';

    Route::get('comprobante','ComprobanteController@inicio');

    Route::group(['middleware'=>'Permiso'],function (){

        //Administrador
        include_once 'super_administrador/usuarios/rutas.php';
        include_once 'super_administrador/modulos/rutas.php';
    });



});
