<?php

use Illuminate\Support\Facades\Route;

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

    Route::group(['middleware'=>'Permiso'],function (){

        //Administrador
        include_once 'super_administrador/usuarios/rutas.php';
        include_once 'super_administrador/modulos/rutas.php';
    });



});
