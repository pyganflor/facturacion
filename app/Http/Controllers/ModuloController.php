<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuloController extends Controller
{
    public function inicio(){
        return view('modulos.inicio');
    }
}
