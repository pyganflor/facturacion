<?php

    Route::get('inventario','InventarioController@inicio');
    Route::post('inventario/store_categoria','InventarioController@storeCategoria');
    Route::post('inventario/estado_categoria','InventarioController@estadoCategoria');
    Route::post('inventario/store_inventario','InventarioController@storeInventario');
    Route::post('inventario/estado','InventarioController@estadoInventario');