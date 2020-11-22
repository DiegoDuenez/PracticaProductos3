<?php

namespace App\Providers;
use App\Modelos\Comentario;
use App\User;
use App\Modelos\Producto;
use App\Policies\ComentarioPolicy;
use App\Policies\ProductoPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Comentario::class => ComentarioPolicy::class,
        Producto::class => ProductoPolicy::class,
        User::class => UserPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
