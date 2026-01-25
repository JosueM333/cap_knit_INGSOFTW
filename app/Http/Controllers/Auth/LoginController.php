<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Constructor
     * SOLO un guest middleware.
     * Nunca mezclar guest de múltiples guards.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * 1. MOSTRAR FORMULARIO DE LOGIN
     * GET /login
     */
    public function showLoginForm()
    {
        if (view()->exists('auth.login')) {
            return view('auth.login');
        }

        return view('login');
    }

    /**
     * 2. PROCESAR LOGIN
     * POST /login
     */
    public function login(Request $request)
    {
        // Validación básica
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        /*
        |--------------------------------------------------------------------------
        | INTENTO 1: ADMIN (guard web)
        |--------------------------------------------------------------------------
        */
        if (
            Auth::guard('web')->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
                $remember
            )
        ) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        /*
        |--------------------------------------------------------------------------
        | INTENTO 2: CLIENTE (guard cliente)
        |--------------------------------------------------------------------------
        */
        if (
            Auth::guard('cliente')->attempt(
                [
                    'CLI_EMAIL' => $request->email,
                    'password' => $request->password,
                ],
                $remember
            )
        ) {
            $request->session()->regenerate();

            return redirect()->intended(route('shop.index'));
        }

        /*
        |--------------------------------------------------------------------------
        | SI FALLAN AMBOS
        |--------------------------------------------------------------------------
        */
        throw ValidationException::withMessages([
            'email' => ['Las credenciales no coinciden con nuestros registros.'],
        ]);
    }

    /**
     * 3. CERRAR SESIÓN
     * POST /logout
     */
    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        if (Auth::guard('cliente')->check()) {
            Auth::guard('cliente')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
