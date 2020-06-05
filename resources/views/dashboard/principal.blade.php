@extends('layouts.app')

@section('content')
    @if(in_array(1,$roles))
        @include('dashboard.administrador')
    @elseif(in_array(2,$roles))
        @include('dashboard.usuario')
    @endif
@endsection
