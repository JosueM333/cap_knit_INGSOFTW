<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'CLI_NOMBRES' => ['required', 'string', 'max:80'],
            'CLI_APELLIDOS' => ['required', 'string', 'max:80'],
            'CLI_EMAIL' => ['required', 'string', 'email', 'max:80', 'unique:CLIENTE'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return Cliente::create([
            'CLI_NOMBRES' => $data['CLI_NOMBRES'],
            'CLI_APELLIDOS' => $data['CLI_APELLIDOS'],
            'CLI_EMAIL' => $data['CLI_EMAIL'],
            'CLI_PASSWORD' => Hash::make($data['password']),
            'CLI_CEDULA' => '9999999999', // Valor por defecto o pedirlo en formulario si es necesario
            'CLI_TELEFONO' => '0000000000',
            'CLI_DIRECCION' => 'Sin dirección',
            'CLI_ESTADO' => 1,
        ]);
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return \Illuminate\Support\Facades\Auth::guard('cliente');
    }
}