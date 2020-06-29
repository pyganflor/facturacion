<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function inicio(){
        return view('comprobantes.factura');
    }
}
