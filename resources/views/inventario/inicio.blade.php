@extends('layouts.app')
@section('title')
    Inventario
@endsection
@section('content')
    <gestion-inventario
        :inventario="{{!isset($inventario) ? json_encode([]): json_encode($inventario)}}"
        :impuestos="{{!isset($impuestos) ? json_encode([]): json_encode($impuestos)}}"
    ></gestion-inventario>
@endsection