<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * BORRADO: protected $redirectTo = '/home';
     * REEMPLAZADO POR LA FUNCIÓN redirectTo() DE ABAJO
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Define que la columna de usuario es CLI_EMAIL
     */
    public function username()
    {
        return 'CLI_EMAIL';
    }

    /**
     * LÓGICA DE REDIRECCIÓN INTELIGENTE
     * Esta función se ejecuta justo después de que el usuario pone bien su clave.
     */
    public function redirectTo()
    {
        // 1. Verificamos si es el Administrador
        // (Asegúrate que 'admin@gmail.com' sea el correo exacto que tiene tu usuario admin en la BD)
        if (auth()->user()->CLI_EMAIL == 'admin@gmail.com') {
            return '/home'; // Lo mandamos al Panel de Gestión
        }

        // 2. Si NO es el admin, asumimos que es un Cliente
        return '/'; // Lo mandamos a la Tienda Pública (Welcome/Index)
    }
}