<?php

namespace App\Http\Controllers\ApiAuth;
use App\User;
use Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Modelos\Comentario;
use App\Modelos\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use App\Mail\Activacion;
use App\Mail\Autorizado;
use App\Mail\SinAutorizacion;

class UsersController extends Controller
{
    public function index(Request $request, $id = null){
        
        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')) {
            if($id){
                $user = Auth::user();
                $accion = "Buscar el usuario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["usuario"=>User::find($id)],200);
            }
            else{
                $user = Auth::user();
                $accion = "Buscar usuarios";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["usuarios"=>User::all(),200]);
            }
            
        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin')) {
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Buscar el usuario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            else{
                $user = Auth::user();
                $accion = "Buscar usuarios";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["user"=>$user],200);
            }
        }
        

    }

    public function registro(Request $request){

        $request ->validate([

            'name'=>'required',
            'years_old'=>'required',
            'email'=> 'required|email|unique:users,email',
            'password'=>'required',
            'imagen'=>'mimes:jpg,bmp,png',


        ]);

        $user = new User();
        $datos['name'] = $user->name = $request->name;
        $user->years_old = $request->years_old;
        $datos['email'] = $user->email = $request->email;
        $user->rol = 'user';
        $user->password = Hash::make($request->password);
        $user->imagen = 'sin imagen';
        $user->estado = 0;
        $datos['codigo'] = $user->codigo_act = Str::random(10);;
        
        if($user->save()){
            //$user = Auth::user();
            /*$correo = Mail::to($request->user()->email)->send(new Activacion($user));
            return response()->json($user, 201);*/
            Mail::send('emails.activacioncorreo', $datos, function ($mail) use ($datos){
                $mail->to($datos['email'], $datos['name'])->subject('Activa tu cuenta para poder logearte')->from('19170154@uttcampus.edu.mx');

            });
            return response()->json($user, 201);

            
        }
        return abort(400, "Hubo problemas al registrarse");

    }

    public function activar($codigo){
        $user = new User();
        $user = User::where('codigo_act', '=', $codigo)->first();
        $user->estado = 1;
        if($user->save()){
            return response()->json("Su cuenta se ha activado",200);
        }
        return abort(400, "No se encontro el usuario");
       

    }

    public function login(Request $request){

        $request->validate([

            'email'=> 'required|email',
            'password'=>'required',
            
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){

            throw ValidationException::withMessages([
                'email|password'=>['Datos incorrectos'],
            ]);


        }
        if($user->estado == 0){
            throw ValidationException::withMessages([
                'estado'=>['La cuenta no se ha activado, verifique su correo'],
            ]);
        }
        else{
            if($user->rol == 'user'){ //SI EL USUARIO TIENE ESTE ROL SE LE ASIGNARAN ESTOS PERMISOS
                $token = $user->createToken($request->email,['user:show','user:save','user:edit','user:delete'])->plainTextToken;
            }
            if($user->rol == 'admin'){ //SI EL USUARIO TIENE ESTE ROL SE LE ASIGNARAN PERMISOS DE ADMINISTRADOR
                $token = $user->createToken($request->email,['admin:admin'])->plainTextToken;
            }
            if($user->rol == 'superuser'){ //SI EL USUARIO TIENE ESTE ROL SE LE ASIGNARAN LOS PERMISOS DE USUARIO/ADMINISTRADOR
                $token = $user->createToken($request->email,['user:admin'])->plainTextToken;
            }
            return response()->json(['token'=>$token], 201);
        }

    
    }

    public function logout(Request $request){

        return response()->json(["Afectados"=>$request->user()->tokens()->delete()],200);

    }

    public function edit(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){

            $user= User::findOrFail($id);

            if($id){

                $user->name = $request->name;
                $user->years_old = $request->years_old;
                $user->email = $request->email; //EL ADMINISTRADOR PUEDE EDITAR A TODOS LOS USUARIOS  
                $user->password = Hash::make($request->password);
                $user->rol = $request->rol;
                $user->estado = $request->estado;
            
                if($user->save()){
                $usuario = Auth::user();
                $accion = "Editar el usuario";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($usuario, $accion, $id));
                return response()->json(["user"=>$user],201);

                }
                
                return response()->json(null,400);
            
            }

            return response()->json(null,400);


        } 
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin')){
            $admin = User::where("rol","=","admin")->select("email")->get();
            $user = Auth::user();
            $accion = "Editar el usuario";
            $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
            return abort(400, "Permisos Invalidos");
        
        }
           
    }

    public function delete(Request $request, $id){

        if($request->user()->tokenCan('admin:admin') || $request->user()->tokenCan('user:admin')){
            
            if($id){
                Comentario::where("usuario", $id)->delete();
                Producto::where("usuario", $id)->delete();
                User::where("id", $id)->delete();
                $user = Auth::user();
                $accion = "Borrar el usuario";
                $correo = Mail::to($request->user()->email)->send(new Autorizado($user, $accion, $id));
                return response()->json(["users"=>User::all()],200);
            }
            return response()->json(null,400);
            

        }
        if(! $request->user()->tokenCan('admin:admin') || ! $request->user()->tokenCan('user:admin')){
            if($id){
                $admin = User::where("rol","=","admin")->select("email")->get();
                $user = Auth::user();
                $accion = "Borrar el usuario";
                $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
                return abort(400, "Permisos Invalidos");
            }
            
        }
            

    }
}
