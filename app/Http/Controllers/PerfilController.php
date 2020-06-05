<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    public function inicio(){
        return view('perfil.inicio',[
            'usuario'=> Auth::user()
        ]);
    }
}
