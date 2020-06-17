<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');


//USUARIOS LOGUEADOS
Route::group(['middleware' => 'auth'],function () {


    Route::post('perfil/update_accesos', 'PerfilController@updateAcessos');
    Route::post('perfil/update_perfil', 'PerfilController@updatePerfil');
    Route::get('cliente','ClienteController@inicio');

    Route::group(['middleware'=>'Permiso'],function (){

        //Administrador
        Route::get('perfil','PerfilController@inicio');

        include_once 'usuarios/rutas.php';

    });



});


//Auth::routes();