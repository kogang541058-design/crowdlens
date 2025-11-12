<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventUserAccessToAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to logout route
        if ($request->is('admin/logout')) {
            return $next($request);
        }

        // If a regular user tries to access admin routes, redirect them
        if (Auth::guard('web')->check() && $request->is('admin/*')) {
            return redirect()->route('dashboard')->with('error', 'Access denied. You do not have admin privileges.');
        }

        return $next($request);
    }
}
