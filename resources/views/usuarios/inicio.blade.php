@extends('layouts.app')
@section('title')
    Usuarios
@endsection
@section('content')
    <gestion-usuario
            :usuarios="{{isset($usuarios) ? $usuarios : json_encode([])}}"
            :modulosactivos="{{$modulos}}"
            :rolesactivos="{{$roles}}"
    ></gestion-usuario>
@endsection