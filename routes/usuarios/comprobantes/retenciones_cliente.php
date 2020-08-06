<?php
    Route::get('retencion_cliente','RetencionClienteController@inicio');
    Route::get('retencion_cliente/list','RetencionClienteController@list');
    Route::post('retencion_cliente/procesar_txt','RetencionClienteController@procesarTxt');
    Route::post('retencion_cliente/procesar_xml','RetencionClienteController@procesarXml');
    Route::post('retencion_cliente/store_retencion_manual','RetencionClienteController@storeRetencionManual');
    Route::post('retencion_cliente/store_retencion_asistido','RetencionClienteController@storeRetencionAsistido');