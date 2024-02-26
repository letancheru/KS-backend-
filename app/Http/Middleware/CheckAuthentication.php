<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request; // Import the correct Request class
use Illuminate\Support\Facades\Auth;

class CheckAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        return response()->json(['message' => 'User is Unauthenticated'], 401);
    }
}
