<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user is not an admin
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect('/user/dashboard'); // Redirect admin to their dashboard
        }

        return $next($request); // Proceed if the user is not an admin
    }
}
