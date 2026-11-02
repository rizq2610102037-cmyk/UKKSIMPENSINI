<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // Redirect berdasarkan role - LANGSUNG CEK ATTRIBUTE
                if ($user->role === 'admin') {
                    return redirect('/admin');
                } elseif ($user->role === 'reservasi') {
                    return redirect()->route('reservasi.dashboard');
                } else {
                    // User role
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}
