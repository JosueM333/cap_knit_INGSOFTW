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

    protected $connection = 'oracle';
    protected $table = 'CLIENTE';
    
    // CAMBIO CLAVE: Poner esto en minúsculas para que coincida con el objeto PHP
    protected $primaryKey = 'cli_id'; 
    
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Si tu tabla usa created_at/updated_at, Laravel espera minúsculas por defecto
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // En el fillable puedes dejarlo así, pero es mejor usar minúsculas si el request viene en minúsculas
    protected $fillable = [
        'cli_nombres',
        'cli_apellidos',
        'cli_cedula',
        'cli_email',
        'cli_telefono',
        'cli_direccion',
        'cli_password',
        'cli_estado'
    ];

    protected $hidden = [
        'cli_password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->cli_password;
    }

    /* MÉTODOS DE VALIDACIÓN (Ajustados a minúsculas) */

    public static function validar(array $datos, $id = null)
    {
        // Convertimos las llaves del array a minúsculas para asegurar consistencia
        $datos = array_change_key_case($datos, CASE_LOWER);

        $reglas = [
            'cli_nombres'   => 'required|string|max:80',
            'cli_apellidos' => 'required|string|max:80',
            // unique:tabla,columna -> Aquí la columna SI va como está en la BD (Oracle usualmente mayúscula o insensible)
            // pero para evitar líos, Laravel suele manejarlo bien. Si falla, prueba 'CLI_CEDULA'.
            'cli_cedula'    => 'required|string|size:10|unique:CLIENTE,CLI_CEDULA' . ($id ? ",$id,CLI_ID" : ''),
            'cli_email'     => 'required|email|max:80|unique:CLIENTE,CLI_EMAIL' . ($id ? ",$id,CLI_ID" : ''),
            'cli_telefono'  => 'required|string|max:15',
            'cli_direccion' => 'required|string|max:100',
            'cli_password'  => $id ? 'nullable|string|min:6' : 'required|string|min:6',
        ];

        $validator = Validator::make($datos, $reglas);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function buscarCliente($criterio)
    {
        return self::where('CLI_ESTADO', 'ACTIVO') // En el Where SÍ usa mayúsculas (SQL)
            ->where(function($q) use ($criterio) {
                $q->where('CLI_CEDULA', 'LIKE', "%$criterio%")
                  ->orWhere('CLI_APELLIDOS', 'LIKE', "%$criterio%")
                  ->orWhere('CLI_EMAIL', 'LIKE', "%$criterio%");
            })
            ->orderBy('CLI_ID', 'DESC')
            ->get();
    }
}