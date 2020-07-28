<?php

    Route::get('factura','FacturaController@inicio');
    Route::get('factura/list','FacturaController@list');
    Route::post('factura/store','FacturaController@store');
    Route::get('factura/comprobante/{id_factura}','FacturaController@pdfFactura');
    Route::post('factura/anular','FacturaController@anular');
    Route::post('factura/reenviar_correo','FacturaController@reenviarCorreo');
    Route::get('factura/editar','FacturaController@editar');
    Route::post('factura/consultar','FacturaController@consultar');