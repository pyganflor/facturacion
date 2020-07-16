<?php

    Route::get('factura','FacturaController@inicio');
    Route::get('factura/list','FacturaController@list');
    Route::post('factura/store','FacturaController@store');
    Route::get('factura/comprobante/{id_factura}','FacturaController@pdfFactura');