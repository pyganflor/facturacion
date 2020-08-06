@extends('layouts.app')
@section('title')
    Retenciones cliente
@endsection
@section('content')
    <gestion-retencion-cliente
        :clientes="{{$clientes}}"
        :facturas="{{$facturas}}"
        :conceptos="{{$conceptosRetencion}}"
    ></gestion-retencion-cliente>
@endsection