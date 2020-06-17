<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class Permiso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $usuario = Auth::user();

        $roles = $usuario->roles->pluck('id_rol');

        if(in_array(2,$roles->toArray())){
            return $next($request);
        }else{
            $request->ajax()
                ? $content = "<div class='text-center'><img src=".asset('archivos/acceso_denegado.jpg')."></div>"
                : $content = view('error.permiso_denegado');

            return new Response($content);
        }

    }
}
