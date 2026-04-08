<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        // El middleware auth:api_cliente valida la firma usando 'jwt'
        // Esto previene el Riesgo 2 (Firma) y el Riesgo 3 (alg:none) indirectamente porque
        // el guard JWT rechaza tokens rotos
        $this->middleware('auth:api_cliente', ['except' => ['login', 'refresh']]);
    }

    public function login(Request $request)
    {
        $credentials = [
            'CLI_EMAIL' => $request->email,
            'password' => $request->password
        ];

        // Autentica usando guard 'api_cliente'
        if (! $token = Auth::guard('api_cliente')->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        // Protegido, retorna los datos asociados al token validado
        return response()->json(Auth::guard('api_cliente')->user());
    }

    public function logout()
    {
        // Mitigación al Riesgo 5: logout() añade el JWT a la Blacklist.
        // Si el usuario envia el token nuevamente tras el logout, será denegado.
        Auth::guard('api_cliente')->logout();

        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    public function refresh()
    {
        try {
            // Mitigación al Riesgo 1: Refrescar obliga a invalidar el token anterior y emitir uno nuevo.
            return $this->respondWithToken(Auth::guard('api_cliente')->refresh());
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo refrescar el token', 'message' => $e->getMessage()], 401);
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl', 15) * 60, // TTL Corto para evitar abuso de robos (Riesgo 1)
            'user' => Auth::guard('api_cliente')->user()
        ]);
    }
}
