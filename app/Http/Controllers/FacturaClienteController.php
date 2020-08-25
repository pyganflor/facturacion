<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacturaClienteController extends Controller
{
    public function inicio(){
        return view('comprobantes.facturas_clientes.inicio');
    }
}
