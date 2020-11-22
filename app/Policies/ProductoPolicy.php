<?php

namespace App\Policies;

use App\User;
use App\Modelos\Producto;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductoPolicy
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

    public function passPro(User $user, Producto $producto){

        return $user->id == $producto->usuario;

    }
}
