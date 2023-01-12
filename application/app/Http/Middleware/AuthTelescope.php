<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthTelescope
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
        if(!in_array(Auth::user()->id, [1,2,3]))
            return redirect('home')->with("error", "Usuario não autorizado!");

        return $next($request);
    }
}
