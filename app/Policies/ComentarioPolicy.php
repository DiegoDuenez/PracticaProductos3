<?php

namespace App\Policies;

use App\User;
use App\Modelos\Comentario;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Mail;
use App\Mail\Activacion;
use App\Mail\Autorizado;
use App\Mail\SinAutorizacion;


class ComentarioPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function pass(User $user, Comentario $comentario){

        return $user->id == $comentario->usuario;

        /*if($user->id != $comentario->usuario){
            $admin = User::where("rol","=","admin")->select("email")->get();
            $user = Auth::user();
            $correotoadmin = Mail::to($admin)->send(new SinAutorizacion($user, $accion, $id));
            return abort(400, "Permisos Invalidos");
        }*/
        

    }
}
