<?php

    Route::get('cliente','ClienteController@inicio');
    Route::post('cliente/store','ClienteController@store');
    Route::post('cliente/estado','ClienteController@estado');