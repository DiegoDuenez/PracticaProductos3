<?php

namespace App\Http\Controllers\ApiAuth;
use App\Modelos\Producto;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Modelos\Comentario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use App\Mail\Autorizado;
use App\Mail\SinAutorizacion;

class ProductosController extends Controller
{
    public function index(Request $request, $id = null){
        
        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin') || $request->user()->tokenCan('user:show')){
            if($id){
                $user = Auth::user();
                $accion = "Buscar el producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["producto"=>Producto::find($id)],200);
            }
            else{
                $user = Auth::user();
                $accion = "Buscar productos";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentarios"=>Producto::all()],200);
            }
            
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:show')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar el producto";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            else{
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar productos";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            
        }
            
    }

    public function userProds(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin') || $request->user()->tokenCan('user:show')){
            if($id){
                $user = Auth::user();
                $accion = "Buscar producto(s) de un usuario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion));
                return response()->json(["producto"=>Producto::where("usuario","=",$id)->get()],200);
            }     
            return abort(400, "Este usuario no tiene productos");
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:show')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar producto(s) de un usuario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            return response()->json(null,400);
            
        }
           
    }

    public function save(Request $request){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){
            
            $producto = new Producto();
            $producto->nombre = $request->nombre;
            $producto->precio = $request->precio;
            $producto->usuario = $request->usuario;

            if($producto->save()){
                $user = Auth::user();
                $accion = "Publicar producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $producto->id));
                return response()->json(["producto"=>$producto],201);

            }
            return response()->json(null,400);

        }
        if($request->user()->tokenCan('user:save')){

            $producto = new Producto();
            $producto->nombre = $request->nombre;
            $producto->precio = $request->precio;
            $producto->usuario = $request->user()->id;

            if($producto->save()){
                $user = Auth::user();
                $accion = "Publicar producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $producto->id));
                return response()->json(["producto"=>$producto],201);

            }
            return response()->json(null,400);

        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:save')){
            $admin = User::where("rol","=","admin")->select("email")->get();
            $user = Auth::user();
            $accion = "Publicar un producto";
            $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $producto->id));
            return abort(400, "Permisos Invalidos");
        }
            
        
        

    }

    public function edit(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){

            $producto = Producto::findOrFail($id);

            if($id){

                $producto->nombre = $request->nombre;
                $producto->precio = $request->precio;
                $producto->usuario = $request->usuario; //EL ADMINISTRADOR PUEDE EDITAR A TODOS LOS USUARIOS  
                
            
                if($producto->save()){
                $usuario = Auth::user();
                $accion = "Editar el producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($usuario, $accion, $id));
                return response()->json(["producto"=>$producto],201);

                }
                
                return response()->json(null,400);
            
            }

            return response()->json(null,400);


        } 
        if($request->user()->tokenCan('user:edit')){

            $producto = Producto::findOrFail($id);
            if($producto->usuario != $request->user()->id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Editar el producto";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            
        
            if($id){
                $producto = new Producto();
                $producto->nombre = $request->nombre;
                $producto->precio = $request->precio;
                $producto->usuario = $request->user()->id; //EL USUARIO COMUN SOLO PUEDE EDITAR SUS PRODUCTOS
                
               
                if($producto->save()){
                    $user = Auth::user();
                    $accion = "Editar el producto";
                    $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                    return response()->json(["producto"=>$producto],201);

                }
                
                
            }
            
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:edit')){
            if($id){
            $admin = User::where("rol","=","admin")->select("email")->get();
            $user = Auth::user();
            $accion = "Editar el producto";
            $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
            return abort(400, "Permisos Invalidos");
            }
        }
            
        

    }

    public function delete(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){

            
            if($id){
                Comentario::where("producto", "=", $id)->delete();
                Producto::where("id", "=", $id)->delete();
                $user = Auth::user();
                $accion = "Borrar el producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["productos"=>Producto::all()],200);
            }
            return response()->json(null,400);

        }
        if($request->user()->tokenCan('user:delete')){

            $producto = Producto::findOrFail($id);
            if($producto->usuario != $request->user()->id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Borrar el producto";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }

            if($id){
                Comentario::where("producto", "=", $id)->delete();
                Producto::where("id", "=", $id)->delete();
                $user = Auth::user();
                $accion = "Borrar el producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["productos"=>Producto::all()],200);
            }
                
            return response()->json(null,400);
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:delete')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Borrar el producto";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            
        }
        

        

    }
}
