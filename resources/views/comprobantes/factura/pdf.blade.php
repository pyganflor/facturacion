<table>
    <tr>
        <td style="width: 341px;">
            <table>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;text-align:center;
                        @if(!isset($data['img_usuario']) && $data['img_usuario']=='')
                                padding:90px 110px
                        @endif"
                    >
                        @if(isset($data['img_usuario']) && $data['img_usuario']!='')
                            <img style="height: 160px;width: 340px" src="{{storage_path('app/public/logo_empresa/').$data['img_usuario']}}">
                        @else
                            NO TIENE LOGO
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid black;border-radius:5px;">
                        <table>
                            <tr>
                                <td style="padding:5px">{{$data['nombre_comercial']}}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px">DIRECCIÓN MATRIZ: {{$data['dir_matriz']}}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px">DIRECCIÓN SUCURSAL: {{$data['dir_establecimiento']}}</td>
                            </tr>
                            <tr>
                                <td style="padding:5px">OBLIGADO A LLEVAR CONTABILIDAD: {{$data['obligado_contabilidad']}}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td style="border:1px solid black;border-radius:5px;width: 354px;">
            <table>
                <tr>
                    <td style="padding:5px">R.U.C.: {{$data['ruc']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">FACTURA</td>
                </tr>
                <tr>
                    <td style="padding:5px">No. {{$data['secuencial']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">NÚMERO DE AUTORIZACIÓN</td>
                </tr>
                <tr>
                    <td> {{$data['clave_acceso']}} </td>
                </tr>
                <tr>
                    <td style="padding:5px">FECHA Y HORA <br/>DE AUTORIZACIÓN: {{$data['fecha_autorizacion']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">AMBIENTE: {{$data['ambiente']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">EMISIÓN: {{$data['tipo_emision']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">CLAVE DE ACCESO</td>
                </tr>
                <tr>
                    <td>
                        @if($data['bar_code'] != '')
                            <img src="data:image/png;base64,{{$data['bar_code']}}" style="width: 350px;height: 50px"/>
                        @else
                            SIN VALOR TRIBUTARIO
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table>
    <tr>
        <td style="border:1px solid black;border-radius:5px;width:706px">
            <table>
                <tr>
                    <td style="padding:5px">Razon social / Nombre y Apellidos: {{$data['razon_social_comprador']}}</td>
                </tr>
                <tr>
                    <td style=";padding:5px">RUC / CI: {{$data['identificacion_comprador']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">Destinatario: {{$data['razon_social_comprador']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">Fecha Emisión: {{$data['fecha_emision']}}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0">
                <tr style="border:1px solid black;width: 750px">
                    <th style="border:1px solid black;width: 105px;text-align: center;">Cod. Principal</th>
                    <th style="border:1px solid black;width: 85px;text-align: center;">Cantidad</th>
                    <th style="border:1px solid black;width: 220px;text-align: center;">Descripción</th>
                    <th style="border:1px solid black;width: 100px;text-align: center;">Precio unitario</th>
                    <th style="border:1px solid black;width: 95px;text-align: center;">Descuento</th>
                    <th style="border:1px solid black;width: 95px;text-align: center;">Precio total</th>
                </tr>
                @foreach($data['artiuclos'] as $key => $articulo)
                    <tr style="width: 750px">
                        <td style="border:1px solid black;padding-left: 5px">{{$articulo['cod_p']}}</td>
                        <td style="border:1px solid black;padding-left: 5px">{{$articulo['cantidad']}}</td>
                        <td style="width: 205px;border:1px solid black;padding-left: 5px">{{$articulo['descripcion']}}</td>
                        <td style="border:1px solid black;padding-left: 5px">{{$articulo['p_unitario']}}</td>
                        <td style="border:1px solid black;padding-left: 5px">{{$articulo['descuento']}}</td>
                        <td style="border:1px solid black;padding-left: 5px">{{$articulo['total']}}</td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>
<table>
    <tr>
        <td style="border:1px solid black;width:403px">
            <table>
                <tr>
                    <td style="padding:5px;text-align: center;"><b>Información Adicional</b></td>
                </tr>
                <tr>
                    <td style="padding:5px">Dirección: {{$data['direccion']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">Teléfono: {{$data['telefono']}}</td>
                </tr>
                <tr>
                    <td style="padding:5px">Correo: {{$data['correo']}}</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="font-size:13px;border:1px solid black;text-align: center">Forma de pago</td>
                    <td style="font-size:13px;border:1px solid black;text-align: center">Valor</td>
                    <td style="font-size:13px;border:1px solid black;text-align: center">Tiempo</td>
                </tr>
                @foreach($data['pagos'] as $pago)
                    <tr>
                        <td style="font-size:13px;border:1px solid black">{{$pago['tipo_pago']}}</td>
                        <td style="font-size:13px;border:1px solid black;text-align: center">{{$pago['total']}}</td>
                        <td style="font-size:13px;border:1px solid black;text-align: center">{{$pago['tiempo']}}</td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td>
            <table  cellpadding="0" cellspacing="0">
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">SUBTOTAL 12%</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['subtotal_12']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">SUBTOTAL 14%</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['subtotal_14']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">SUBTOTAL 0%</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['subtotal_0']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">SUBTOTAL No objeto de IVA</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['subtotal_no_objeto']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">SUBTOTAL SIN IMPUESTO</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['subtotal_sin_imp']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">SUBTOTAL Exento de IVA</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['subtotal_exento']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">DESCUENTO</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['total_descuento']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">ICE</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['ice']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">IVA 12%</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['iva_12']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">IVA 14%</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['iva_14']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">IRBPNR</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['IRBPNR']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">PROPINA</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['propina']}}</td>
                </tr>
                <tr>
                    <td style="font-size:13px;border: 1px solid black;width:200px;padding-left: 6px">VALOR TOTAL</td>
                    <td style="font-size:13px;border: 1px solid black;width: 85px;text-align: right;padding-left: 6px">{{$data['total']}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<style>
    *{
        font-family: "Nunito", sans-serif;
        font-size: 13px;
    }
</style>