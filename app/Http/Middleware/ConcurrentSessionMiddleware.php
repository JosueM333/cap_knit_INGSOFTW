<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ConcurrentSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Solo verificamos si el usuario envió un token válido (check() retorna true)
        if (Auth::guard('api_cliente')->check()) {
            // Obtenemos el identificador único de este token (JTI)
            $payload = Auth::guard('api_cliente')->payload();
            $jti = $payload->get('jti');
            $userId = Auth::guard('api_cliente')->id();
            
            // Consultamos en caché cuál es el JTI que se generó en el último login de este usuario
            $validJti = Cache::get('cliente_session_' . $userId);

            // Si existe un JTI guardado pero no coincide con el JTI del token actual
            if ($validJti && $validJti !== $jti) {
                return response()->json([
                    'error' => 'Sesión terminada.',
                    'message' => 'El sistema detectó un inicio de sesión más reciente en otro dispositivo (Evitando sesiones concurrentes - Riesgo 8).'
                ], 401);
            }
        }

        return $next($request);
    }
}
