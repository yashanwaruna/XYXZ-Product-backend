<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    
    public function handle($request, Closure $next)
    {
        if(\Auth::User() == null){
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
