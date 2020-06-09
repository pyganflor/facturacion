@extends('layouts.app')
@section('title')
    Perfil
@endsection
@section('content')
    <perfil-component :usuario="{{$usuario}}"></perfil-component>
@endsection