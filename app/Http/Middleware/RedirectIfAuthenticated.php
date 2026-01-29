<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
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

            // Verificamos si hay sesión activa en este guard
            if (Auth::guard($guard)->check()) {

                // 1. Si el que está logueado es ADMIN ('web')
                if ($guard === 'web') {
                    // PROTECCIÓN ANTI-BUCLE: Si ya estamos en 'home', no redirigir.
                    if ($request->routeIs('home')) {
                        return $next($request);
                    }
                    return redirect()->route('home');
                }

                // 2. Si el que está logueado es CLIENTE ('cliente')
                if ($guard === 'cliente') {
                    // PROTECCIÓN ANTI-BUCLE: Si ya estamos en la tienda, no redirigir.
                    if ($request->routeIs('shop.index') || $request->is('/')) {
                        return $next($request);
                    }
                    return redirect()->route('shop.index');
                }
            }
        }

        return $next($request);
    }
}