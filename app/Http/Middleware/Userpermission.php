<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Userpermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to registration routes for guests
        if (($request->routeIs('sign_up') || $request->routeIs('sign_up.user')) && !Auth::check()) {
            return $next($request);
        }

        // For authenticated users, check if they have admin level 3
        if (Auth::check() && auth()->user()->is_admin == 3) {
            return $next($request);
        }

        // If user is authenticated but doesn't have permission
        if (Auth::check()) {
            return redirect()->route('homepage')->with('error', 'You do not have permission');
        }

        // For guests trying to access other protected routes
        return redirect()->route('login')->with('error', 'Please login to access this page');
    }
}