<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Cliente extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'CLIENTE';
    protected $primaryKey = 'CLI_ID';
    public $timestamps = false;

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

    protected $hidden = [
        'CLI_PASSWORD',
    ];

    // Mapeo para que Laravel identifique la contraseña en el esquema personalizado
    public function getAuthPassword()
    {
        return $this->CLI_PASSWORD;
    }

    // Indica a Eloquent el nombre de la columna de credenciales
    public function getAuthPasswordName()
    {
        return 'CLI_PASSWORD';
    }

    // Reglas de validación para registro y actualización de clientes
    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'CLI_NOMBRES' => 'required|string|max:80',
            'CLI_APELLIDOS' => 'required|string|max:80',
            'CLI_CEDULA' => 'required|string|size:10|unique:CLIENTE,CLI_CEDULA' . ($id ? ",$id,CLI_ID" : ''),
            'CLI_EMAIL' => 'required|email|max:80|unique:CLIENTE,CLI_EMAIL' . ($id ? ",$id,CLI_ID" : ''),
            'CLI_TELEFONO' => 'required|string|max:15',
            'CLI_DIRECCION' => 'required|string|max:100',
            'CLI_PASSWORD' => $id ? 'nullable|string|min:6' : 'required|string|min:6',
        ];

        $mensajes = [
            'CLI_NOMBRES.required' => 'Los nombres son obligatorios',
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // Cifra la contraseña y crea el nuevo registro
    public static function guardarCliente(array $datos)
    {
        $datos['CLI_PASSWORD'] = Hash::make($datos['CLI_PASSWORD']);
        return self::create($datos);
    }

    // Retorna únicamente los clientes con estado ACTIVO
    public static function obtenerClientes()
    {
        return self::where('CLI_ESTADO', 'ACTIVO')->get();
    }

    // Búsqueda multicanal por cédula, correo o apellidos
    public static function buscarCliente($criterio)
    {
        return self::where('CLI_CEDULA', 'LIKE', "%$criterio%")
            ->orWhere('CLI_EMAIL', 'LIKE', "%$criterio%")
            ->orWhere('CLI_APELLIDOS', 'LIKE', "%$criterio%")
            ->get();
    }

    // Actualiza datos y gestiona el re-cifrado de contraseña si fue modificada
    public function actualizarCliente(array $datos)
    {
        if (!empty($datos['CLI_PASSWORD'])) {
            $datos['CLI_PASSWORD'] = Hash::make($datos['CLI_PASSWORD']);
        } else {
            unset($datos['CLI_PASSWORD']);
        }
        $this->update($datos);
    }

    // Realiza un borrado lógico cambiando el estado a INACTIVO
    public function desactivarCliente()
    {
        $this->CLI_ESTADO = 'INACTIVO';
        $this->save();
    }
}