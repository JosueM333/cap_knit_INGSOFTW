<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Aquí definimos que, por defecto, se use el guard 'web' (para admins).
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Definimos DOS guards:
    | 1. 'web': Para administradores (usa la sesión normal).
    | 2. 'cliente': Para tus clientes de la tienda.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users', // Apunta al provider 'users' de abajo
        ],

        'cliente' => [
            'driver' => 'session',
            'provider' => 'clientes', // Apunta al provider 'clientes' de abajo
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Aquí está la clave. Separamos las tablas:
    | 1. 'users': Busca en el modelo User (tabla users) -> ADMINS
    | 2. 'clientes': Busca en el modelo Cliente (tabla CLIENTE) -> COMPRADORES
    |
    */

    'providers' => [
        // Proveedor para Administradores
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, // CORREGIDO: Apunta a User, no a Cliente
        ],

        // Proveedor para Clientes
        'clientes' => [
            'driver' => 'eloquent',
            'model' => App\Models\Cliente::class, // NUEVO: Apunta a tu modelo Cliente
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Configuración para recuperar contraseñas.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Opcional: Configuración para recuperar contraseña de clientes
        'clientes' => [
            'provider' => 'clientes',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

];