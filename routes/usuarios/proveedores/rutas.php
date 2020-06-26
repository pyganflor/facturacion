<?php

    Route::get('proveedor','ProveedorController@inicio');
    Route::post('proveedor/store','ProveedorController@store');
    Route::post('proveedor/estado','ProveedorController@estado');
