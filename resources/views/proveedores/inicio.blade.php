@extends('layouts.app')
@section('title')
   Proveedores
@endsection
@section('content')
    <gestion-proveedores :proveedores="{{$proveedores}}"></gestion-proveedores>
@endsection

