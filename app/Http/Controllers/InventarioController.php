<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Model\{ArticuloCategoriaInventario, ArticuloImpuesto, CategoriaInventario, Inventario, Impuesto};
use Illuminate\Http\Request;
use App\Http\Requests\RequestStoreInventario;

class InventarioController extends Controller
{
    public function inicio(){

        return view('inventario.inicio',[
            'inventario' => Inventario::where('id_usuario',Auth::id())->with('categorias')->first(),
            'impuestos' => Impuesto::where('tipo','factura')->with('tipo_impuesto')->get(),
        ]);
    }

    public function storeCategoria(Request $request){

        $request->validate([
            'categoria' =>'required',
            'estado' => 'required'
        ],[
            'categoria.required'=> 'El nombre de la categoría es obligatorio',
            'estado.required'=>'No se obtuvo el estado de la categoría'
        ]);

        try{

            $usuario=Auth::user();

            if(!isset($usuario->inventario))
                Inventario::create(['id_usuario'=> $usuario->id_usuario]);

            $categoria = CategoriaInventario::updateOrCreate(
                ['id_categoria_inventario'=> $request->id_categoria_inventario],
                [
                    'id_inventario' => $usuario->inventario->id_inventario,
                    'categoria'=> $request->categoria,
                    'estado' => $request->estado
                ]
            );

            $idCategoria = $categoria->id_categroria_inventario;
            if(!isset($categoria->id_categoria_inventario))
                $idCategoria = CategoriaInventario::orderBy('id_categoria_inventario','desc')->first()->id_categoria_inventario;

        }catch (\Exception $e){
            return HomeController::catch($e);
        }

        return response()->json([
            'categoria' => $categoria,
            'idCategoria' => $idCategoria,
            'msg' => 'La categoría se ha guardado'
        ]);

    }

    public function estadoCategoria(Request $request){

        $request->validate([
            'id_categoria_inventario'=> 'required|exists:categoria_inventario,id_categoria_inventario',
            'estado'=> 'required'
        ],[
            'id_categoria_inventario.required'=> 'No se obtuvo el identificador de la categoría',
            'id_categoria_inventario.exists'=> 'La categoria no se encuentra registrada',
            'estado.required' => 'No se obtuvo el estado',
        ]);

        try{

            $categoria = CategoriaInventario::find($request->id_categoria_inventario);
            $categoria->update(['estado'=> $request->estado!="true"]);

            return response()->json([
                'msg' => 'La categoría ha sido '.($request->estado ? 'desactivada': 'activada'),
                'categoria' => $categoria
            ],200);


        }catch (\Exception $e){
            return HomeController::catch($e);
        }
    }

    public function storeInventario(RequestStoreInventario $request){

       try{

           $idCatgInv = Auth::user()->inventario->categorias->pluck('id_categoria_inventario')->toArray();

           $cant = ArticuloCategoriaInventario::whereIn('id_categoria_inventario',$idCatgInv)->count();

           $articulo = ArticuloCategoriaInventario::updateOrCreate(
               ['id_articulo_categoria_inventario'=> $request->id_articulo_categoria_inventario],
               [
                   'id_categoria_inventario' => $request->id_categoria_inventario,
                   'articulo' => $request->articulo, 'neto' => $request->neto,
                   'stock' => $request->stock,
                   'codigo_p' => !isset($request->codigo_p) ? 'ART'.str_pad(($cant+1),6,0,STR_PAD_LEFT) : $request->codigo_p, 'codigo_a' => $request->codigo_a, 'und' => $request->und,
               ]
           );

           if(!isset($articulo->id_articulo_categoria_inventario))
               $articulo = ArticuloCategoriaInventario::orderBy('id_articulo_categoria_inventario','desc')->first();

           $oldArtImp = ArticuloImpuesto::where('id_articulo_categoria_inventario',$articulo->id_articulo_categoria_inventario)->pluck('id_articulo_impuesto');

           foreach ($request->impuestos as $impuesto) {

                $imp = json_decode($impuesto);

                ArticuloImpuesto::create([
                   'id_articulo_categoria_inventario'=> $articulo->id_articulo_categoria_inventario,
                    'id_impuesto' => $imp->id_impuesto,
                    'id_tipo_impuesto' => $imp->id_tipo_impuesto
                ]);

           }

           ArticuloImpuesto::destroy($oldArtImp);

           return response()->json([
               'msg'=>'El artículo se ha guardado',
               'articulo' => ArticuloCategoriaInventario::where('id_articulo_categoria_inventario',$articulo->id_articulo_categoria_inventario)
                                    ->with('categoria')->with('impuestos')->first()
           ]);

       }catch(\Exception $e){
           return HomeController::catch($e);
       }
    }

    public function estadoInventario(Request $request){

        $request->validate([
            'id_articulo_categoria_inventario'=> 'required|exists:articulo_categoria_inventario,id_articulo_categoria_inventario',
            'estado'=> 'required'
        ],[
            'id_articulo_categoria_inventario.required'=> 'No se obtuvo el identificador del artículo',
            'id_articulo_categoria_inventario.exists'=> 'El artículo no se encuentra registrado',
            'estado.required' => 'No se obtuvo el estado',
        ]);

        try{

            $articulo = ArticuloCategoriaInventario::find($request->id_articulo_categoria_inventario);
            $articulo->update(['estado'=> $request->estado!="true"]);

            return response()->json([
                'msg' => 'el artículo ha sido '.($request->estado ? 'desactivado': 'activado'),
                'articulo' => $articulo
            ],200);


        }catch (\Exception $e){
            return HomeController::catch($e);
        }



    }
}
