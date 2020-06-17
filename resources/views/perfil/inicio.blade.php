@extends('layouts.app')
@section('title')
    Perfil
@endsection
@section('content')
    <perfil-component
            :usuario="{{$usuario}}"
            :perfil="{{$usuario->perfil == null ? json_encode([]) : $usuario->perfil}}"
            :storage="'{{$storage}}'"
    ></perfil-component>
@endsection