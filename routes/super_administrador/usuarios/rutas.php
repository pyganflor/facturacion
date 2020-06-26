<?php

    Route::get('usuario','UsuarioController@inicio');
    Route::post('usuario/store','UsuarioController@store');
    Route::post('usuario/estado','UsuarioController@estado');