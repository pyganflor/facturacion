@extends('layouts.app')
@section('title')
    Perfil
@endsection
@section('content')
    <perfil-component
            :usuario="{{$usuario}}"
            :perfil="{{$usuario->perfil == null ? json_encode(['']) : $usuario->perfil}}"
            :storage="'{{\Illuminate\Support\Facades\Storage::url('img_user')}}'"
    ></perfil-component>
@endsection