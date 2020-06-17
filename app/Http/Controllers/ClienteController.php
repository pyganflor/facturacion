<?php

namespace App\Http\Controllers;

use App\Model\Cliente;
use App\Model\Impuesto;
use App\Model\TipoIdentificacion;
use App\Model\TipoPago;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function inicio()
    {
        return view('clientes.inicio',[
            'clientes' => Cliente::orderby('nombre','asc')->get(),
            'impuestos' => Impuesto::where('tipo','factura')->with('tipo_impuesto')->get(),
            'tipoPago' => TipoPago::orderBy('nombre','asc')->get(),
            'tipoIdentificacion' => TipoIdentificacion::orderBy('nombre','asc')->get()
        ]);
    }
}
