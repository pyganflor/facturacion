@extends('layouts.app')
@section('title')
    Facturas clientes
@endsection
@section('content')
    <gestion-factura-proveedor
            :sustento_tributario="{{$sustentoTributario}}"
            :proveedores="{{$proveedores}}"
            :inventario="{{$inventario}}"
    ></gestion-factura-proveedor>
@endsection
