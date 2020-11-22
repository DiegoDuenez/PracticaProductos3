<?php

namespace App\Http\Middleware;

use Closure;

class VerificarEdad
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
        if($request->years_old <= 17)
            return abort(400, "Uno de los datos que se ingreso no es valido");
        return $next($request);
    }
}
