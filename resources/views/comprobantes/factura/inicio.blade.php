@extends('layouts.app')
@section('title')
    Facturas
@endsection
@section('content')
    <gestion-factura
            :clientes="{{$clientes}}"
            :factureros="{{$factureros}}"
            :sustento_tributario="{{$sustentoTributario}}"
            :punto_emision="{{$puntoEmision}}"
            :tipopago="{{$tiposPago}}"
            :inventario="{{$inventario}}"
            :tipoidentificacion="{{$tipoIdentificacion}}"
            :paises="{{$paises}}"
    ></gestion-factura>
@endsection
