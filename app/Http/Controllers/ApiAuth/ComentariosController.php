<?php

namespace App\Http\Controllers\ApiAuth;
use App\Modelos\Comentario;
use App\Modelos\Producto;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use App\Mail\Autorizado;
use App\Mail\SinAutorizacion;

class ComentariosController extends Controller
{
    public function index(Request $request, $id = null){
        
        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')) {
            if($id){
                $user = Auth::user();
                $accion = "Buscar el comentario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentario"=>Comentario::find($id)],200);
            }
            else{
                $user = Auth::user();
                $accion = "Buscar comentarios";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentarios"=>Comentario::all()],200);
            }
        }
        if($request->user()->tokenCan('user:show')){

            if($id){
                $user = Auth::user();
                $accion = "Buscar el comentario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentario"=>Comentario::find($id)],200);
            }
            else{
            $user = Auth::user();
            $accion = "Buscar comentarios";
            $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
            return response()->json(["comentarios"=>Comentario::all()],200);
            }
            
        
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:show')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar el comentario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            else{
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar comentarios";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            
            
        }
        
    }

    public function usersCom(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin') || $request->user()->tokenCan('user:show')){

            if($id){
                $user = Auth::user();
                $accion = "Buscar comentarios de el usuario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentarios"=>Comentario::all()->where('usuario','=', $id)]);
            }
            return response()->json(null,400);

        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:show')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar comentarios de el usuario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            return response()->json(null,400);
        }
        
        
    }

    public function prodsCom(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin') || $request->user()->tokenCan('user:show')){

            if($id){
                $user = Auth::user();
                $accion = "Buscar comentarios de el producto";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentarios"=>Comentario::all()->where('producto','=', $id)]);
            }
            return response()->json(null,400);

        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:show')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar comentarios de el usuario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            return response()->json(null,400);
        }
            
       
        
    }

    public function save(Request $request){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){

            $comentario = new Comentario();
            $comentario->titulo = $request->titulo;
            $comentario->contenido = $request->contenido;
            $comentario->usuario = $request->usuario;   //EL ADMINISTRADOR PUEDE INSERTAR COMENTARIOS CON EL ID DE LA PERSONA QUE SEA
            $prod = $comentario->producto = $request->producto;

            
            if($comentario->save()){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Hacer comentario";
                $correo = Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $comentario->id));
                return response()->json(["comentario"=>$comentario],201);
            }
            return response()->json(null,400);

        }
        if($request->user()->tokenCan('user:save')){

            
            $comentario = new Comentario();
            $comentario->titulo = $request->titulo;
            $comentario->contenido = $request->contenido;
            $comentario->usuario =  $request->user()->id;  //UN USUARIO COMUN SOLO PUEDE GUARDAR COMENTARIOS CON SU ID (NO ES NECESARIO ESPECIFICAR EL CAMPO Y SU VALOR EN INSOMNIA)
            $prod = $comentario->producto = $request->producto;

            if($comentario->save()){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Hacer el comentario";
                $correo = Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $comentario->id));
                return response()->json(["comentario"=>$comentario],201);
            }
            return response()->json(null,400);
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:save')){
            $comentario = new Comentario();
            $admin = User::where("rol","=","admin")->select("email")->get();
            $user = Auth::user();
            $accion = "Hacer un comentario";
            $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $comentario->id));
            return abort(400, "Permisos Invalidos");
    
        }


    }

    public function edit(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){

            $comentario = Comentario::findOrFail($id);

            if($id){

                $comentario->titulo = $request->titulo;
                $comentario->contenido = $request->contenido;
                $comentario->usuario = $request->usuario; //EL ADMINISTRADOR PUEDE EDITAR TODOS LOS COMENTARIOS SIN RESTRICCIONES 
                $comentario->producto = $request->producto;
            
                if($comentario->save()){
                    $user = Auth::user();
                    $accion = "Editar el comentario";
                    $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                    return response()->json(["comentario"=>$comentario],201);

                 }
                
                return response()->json(null,400);
            
            }

            return response()->json(null,400);


        } 
        if($request->user()->tokenCan('user:edit')){

            $comentario = Comentario::findOrFail($id);
            if($comentario->usuario != $request->user()->id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Editar el comentario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            /*if(! $this->authorize('pass', $comentario)){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Borrar el comentario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }*/

            if($id){
                $comentario->titulo = $request->titulo;
                $comentario->contenido = $request->contenido;
                $comentario->usuario = $request->user()->id; //EL USUARIO COMUN SOLO PUEDE EDITAR SUS COMENTARIOS
                $comentario->producto = $request->producto;
               
                if($comentario->save()){
                    $user = Auth::user();
                    $accion = "Editar el comentario";
                    $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                    return response()->json(["comentario"=>$comentario],201);

                }
                    
                return response()->json(null,400);
            }
            
        

        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:edit')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Editar el comentario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            return response()->json(null,400);
        }
               


    }

    public function delete(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){

            Comentario::destroy($id);
            if($id){
                $user = Auth::user();
                $accion = "Borrar el comentario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentarios"=>Comentario::all()],200);
            }
            return response()->json(null,400);

        }
        if($request->user()->tokenCan('user:delete')){

           
            $comentario = Comentario::find($id);
            if($comentario->usuario != $request->user()->id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Borrar el comentario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            $comentario->delete();

            if($id){
                $user = Auth::user();
                $accion = "Borrar el comentario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["comentarios"=>Comentario::all()],200); //QUE EL USUARIO SOLO PUEDA ELIMINAR SUS COMENTARIOS
            }
            return response()->json(null,400);
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin') || ! $request->user()->tokenCan('user:delete')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Borrar el comentario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            return response()->json(null,400);
        }
        
        

    }
}
