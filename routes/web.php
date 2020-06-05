<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');


//USUARIOS LOGUEADOS
Route::group(['middleware' => 'auth'],function () {

    Route::get('perfil','PerfilController@inicio');

});


//Auth::routes();