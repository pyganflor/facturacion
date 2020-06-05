<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $roles= Auth::user()->roles->pluck('id_rol')->toArray();

        return view('dashboard.principal',[
            'roles'=> $roles
        ]);
    }

    public static function catch($e){
        return response()->json([
            'errors'=>[
                'mensaje'=> $e->getMessage().' <br /> en la linea '. $e->getLine(). ' <br /> del archivo '.$e->getFile(). '<br /> trace: '. $e->getTraceAsString()
            ]
        ],500);
    }
}
