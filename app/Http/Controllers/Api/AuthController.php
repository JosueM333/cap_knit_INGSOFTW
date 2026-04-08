<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api_cliente', ['except' => ['login', 'refresh']]);
    }

    public function login(Request $request)
    {
        $credentials = [
            'CLI_EMAIL' => $request->email,
            'password' => $request->password
        ];

        if (! $token = Auth::guard('api_cliente')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        // --- RIESGO 8 (Sesiones concurrentes) ---
        // Al generarse un login exitoso, extraemos su JWT ID único.
        $jti = Auth::guard('api_cliente')->payload()->get('jti');
        $userId = Auth::guard('api_cliente')->id();
        
        // Guardamos o sobreescribimos en caché "cuál es el único token JTI váldo hoy".
        Cache::put('cliente_session_' . $userId, $jti);
        // -----------------------------------------

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(Auth::guard('api_cliente')->user());
    }

    public function logout()
    {
        $userId = Auth::guard('api_cliente')->id();

        Auth::guard('api_cliente')->logout();

        // --- RIESGO 8: Limpiamos la caché de sesión a nivel usuario ---
        Cache::forget('cliente_session_' . $userId);

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    public function refresh()
    {
        try {
            // --- RIESGO 6 (Rotación de Refresh) ---
            // 'refresh()' obliga a desechar el token anterior, añadiéndolo automáticamente
            // a la blacklist para que no vuelva a funcionar, logrando la rotación perfecta.
            $newToken = Auth::guard('api_cliente')->refresh();
            
            // --- RIESGO 8: Al refrescar se emitió un JTI nuevo, hay que guardarlo
            $jti = Auth::guard('api_cliente')->setToken($newToken)->payload()->get('jti');
            $userId = Auth::guard('api_cliente')->id();
            Cache::put('cliente_session_' . $userId, $jti);

            return $this->respondWithToken($newToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo refrescar el token', 'message' => $e->getMessage()], 401);
        }
    }

    protected function respondWithToken($token)
    {
        $ttlMinutes = config('jwt.ttl', 15);

        // --- RIESGO 7 (Almacenamiento inseguro / XSS) ---
        // Por seguridad el token debe depositarse en una galleta HttpOnly
        // Esto le prohíbe explícitamente a JavaScript (y a un XSS) poder accesar a su valor.
        $cookie = cookie(
            'token',                       // Nombre del cookie
            $token,                        // Nuestro Access JWT
            $ttlMinutes,                   // Expiración calculada
            '/',                           // Path global
            null,                          // Dominio genérico
            config('app.env') !== 'local', // Secure (true online / false localhost)
            true,                          // HttpOnly (ESTO MITIGA EL RIESGO DIRECTAMENTE)
            false,                         // Raw
            'Strict'                       // SameSite
        );
        // -------------------------------------------------

        return response()->json([
            'access_token' => $token, // Lo dejo en el JSON solo para fines didácticos en Postman (en un escenario ideal productivo no se regresa en body)
            'token_type' => 'bearer',
            'expires_in' => $ttlMinutes * 60,
            'message' => 'Verifica tus Cookies del navegador. Se envió el token HttpOnly de forma segura.',
            'user' => Auth::guard('api_cliente')->user()
        ])->cookie($cookie);
    }
}
