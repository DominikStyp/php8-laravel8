<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class IsAdminBySession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $isAdmin = $request->session()->get('is_admin', false);
        if(!$isAdmin)
            throw new UnauthorizedException("You're not an admin!");
        return $next($request);
    }
}
