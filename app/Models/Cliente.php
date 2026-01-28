<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Traits\OracleCompatible;

class Cliente extends Authenticatable
{
    use HasFactory, Notifiable, OracleCompatible;

    protected $connection = 'oracle_guayaquil';
    protected $table = 'CLIENTE';
    protected $primaryKey = 'CLI_ID';

    public $timestamps = true;



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
        // Se eliminó remember_token
    ];

    /**
     * Laravel: devolver el valor de la contraseña
     */
    public function getAuthPassword()
    {
        return $this->CLI_PASSWORD;
    }

    /**
     * Laravel: decirle cuál es el nombre REAL del campo password
     */
    public function getAuthPasswordName()
    {
        return 'CLI_PASSWORD';
    }

    /* =========================================================
       MÉTODOS DE LÓGICA DE NEGOCIO
       ========================================================= */

    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'CLI_NOMBRES' => 'required|string|max:80',
            'CLI_APELLIDOS' => 'required|string|max:80',
            'CLI_CEDULA' => 'required|string|size:10|unique:oracle_guayaquil.CLIENTE,CLI_CEDULA' . ($id ? ",$id,CLI_ID" : ''),
            'CLI_EMAIL' => 'required|email|max:80|unique:oracle_guayaquil.CLIENTE,CLI_EMAIL' . ($id ? ",$id,CLI_ID" : ''),
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

    public static function guardarCliente(array $datos)
    {
        $datos['CLI_PASSWORD'] = Hash::make($datos['CLI_PASSWORD']);
        // El estado se guardará como 'ACTIVO' por defecto desde la BDD si no se envía
        return self::create($datos);
    }

    public static function obtenerClientes()
    {
        // CORREGIDO: Filtra por el string 'ACTIVO'
        return self::where('CLI_ESTADO', 'ACTIVO')->get();
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
        // CORREGIDO: Actualiza a 'INACTIVO'
        $this->CLI_ESTADO = 'INACTIVO';
        $this->save();
    }
}