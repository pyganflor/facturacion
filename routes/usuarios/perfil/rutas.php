<?php
    Route::get('perfil','PerfilController@inicio');
    Route::post('perfil/update_accesos', 'PerfilController@updateAcessos');
    Route::post('perfil/update_perfil', 'PerfilController@updatePerfil');