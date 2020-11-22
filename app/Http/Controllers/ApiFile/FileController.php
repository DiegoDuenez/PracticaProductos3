<?php

namespace App\Http\Controllers\ApiFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Autorizado;
use App\Mail\SinAutorizacion;

class FileController extends Controller
{
    public function saveImg(Request $request){

        if($request->user()->tokenCan("user:save") && $request->user()->tokenCan("user:edit") || $request->user()->tokenCan("admin:admin") || $request->user()->tokenCan("user:admim")){

            if($request->hasFile('file')){

                $user = new User();
                $extension = $request->file('file')->extension();
                $path = Storage::disk('public')->put('ProfilePictures/', $request->file);
                $user = Auth::user();
                $user->imagen = $path;
                $user->save();
                $user = Auth::user();
                $accion = "Subir imagen";
                $correo= Mail::to($request->user()->email)->send(new Autorizado($user, $accion));
                return response()->json(["Path" => $path], 201);
            }
            return response()->json(null,400);
            
        }
        else{
            $user = Auth::user();
            $accion = "Subir imagen";
            $correotoadmin = Mail::to("19170154@uttcampus.edu.mx")->send(new SinAutorizacion($user, $accion));
            return abort(400, "Permisos Invalidos");
            
        }


    }
}


            /*$img = $request->file('file');
            $imgname = $img->getClientOriginalName();
            $imgname = pathinfo($imgname, PATHINFO_FILENAME);
            $name_img = str_replace(" ", "_", $imgname);

            $extension = $img->getClientOriginalName();
            $picture = date("His") . "-" . $name_img . "." . $extension;
            $img->move(public_path('ProfilePictures/'), $picture);

            $user = new User();
            //$user = User::find($id);
            $user = Auth::user();
            $user->imagen = $picture;
            $user->save();
            
            return response()->json(["Mensaje" => "La imagen se guardo correctamente"], 201);*/