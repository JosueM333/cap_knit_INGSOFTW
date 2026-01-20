<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// 1. IMPORTANTE: Usamos Authenticatable en lugar de Model
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

// 2. IMPORTANTE: Extendemos de Authenticatable
class Cliente extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'CLIENTE';
    protected $primaryKey = 'CLI_ID';
    public $timestamps = false;

    const CREATED_AT = 'CLI_CREATED_AT';
    const UPDATED_AT = 'CLI_UPDATED_AT';

    protected $fillable = [
        'CLI_NOMBRES',
        'CLI_APELLIDOS',
        'CLI_CEDULA',
        'CLI_EMAIL',
        'CLI_TELEFONO',
        'CLI_DIRECCION',
        'CLI_PASSWORD',
        'CLI_ESTADO'
    ];

    // 3. IMPORTANTE: Ocultar password y token por seguridad
    protected $hidden = [
        'CLI_PASSWORD',
        'remember_token',
    ];

    // 4. IMPORTANTE: Decirle a Laravel cuál es la columna de la contraseña
    public function getAuthPassword()
    {
        return $this->CLI_PASSWORD;
    }

    /* =========================================================
       TUS MÉTODOS DE LÓGICA DE NEGOCIO (SE MANTIENEN IGUAL)
       ========================================================= */

    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'CLI_NOMBRES'   => 'required|string|max:80',
            'CLI_APELLIDOS' => 'required|string|max:80',
            'CLI_CEDULA'    => 'required|string|size:10|unique:CLIENTE,CLI_CEDULA' . ($id ? ",$id,CLI_ID" : ''),
            'CLI_EMAIL'     => 'required|email|max:80|unique:CLIENTE,CLI_EMAIL' . ($id ? ",$id,CLI_ID" : ''),
            'CLI_TELEFONO'  => 'required|string|max:15',
            'CLI_DIRECCION' => 'required|string|max:100',
            'CLI_PASSWORD'  => $id ? 'nullable|string|min:6' : 'required|string|min:6',
        ];

        // ... (Tus mensajes personalizados aquí) ...
        $mensajes = [
            'CLI_NOMBRES.required' => 'Los nombres son obligatorios',
            // ... el resto de tus mensajes ...
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function guardarCliente(array $datos)
    {
        $datos['CLI_PASSWORD'] = Hash::make($datos['CLI_PASSWORD']);
        return self::create($datos);
    }

    public static function obtenerClientes()
    {
        return self::where('CLI_ESTADO', 1)->get();
    }

    public static function buscarCliente($criterio)
    {
        return self::where('CLI_CEDULA', 'LIKE', "%$criterio%")
                    ->orWhere('CLI_EMAIL', 'LIKE', "%$criterio%")
                    ->orWhere('CLI_APELLIDOS', 'LIKE', "%$criterio%")
                    ->get();
    }

    public function actualizarCliente(array $datos)
    {
        if (!empty($datos['CLI_PASSWORD'])) {
            $datos['CLI_PASSWORD'] = Hash::make($datos['CLI_PASSWORD']);
        } else {
            unset($datos['CLI_PASSWORD']);
        }
        $this->update($datos);
    }

    public function desactivarCliente()
    {
        $this->CLI_ESTADO = 0;
        $this->save();
    }
}