@extends('layouts.app')

@section('content')
    <perfil-component :usuario="{{$usuario}}"></perfil-component>
@endsection