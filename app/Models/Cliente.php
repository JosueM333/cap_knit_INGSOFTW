<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'CLIENTE';
    protected $primaryKey = 'CLI_ID';
    public $timestamps = true;

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

        
        $mensajes = [
            'CLI_NOMBRES.required' => 'Los nombres del cliente son obligatorios',
            'CLI_NOMBRES.max'      => 'Los nombres del cliente no pueden exceder los 80 caracteres',

            'CLI_APELLIDOS.required' => 'Los apellidos del cliente son obligatorios',
            'CLI_APELLIDOS.max'      => 'Los apellidos del cliente no pueden exceder los 80 caracteres',

            'CLI_CEDULA.required' => 'La cédula del cliente es obligatoria',
            'CLI_CEDULA.size'     => 'La cédula debe tener 10 dígitos',
            'CLI_CEDULA.unique'   => 'Cliente ya registrado',

            'CLI_EMAIL.required' => 'El correo electrónico es obligatorio',
            'CLI_EMAIL.email'    => 'El correo electrónico no tiene un formato válido',
            'CLI_EMAIL.max'      => 'El correo electrónico no puede exceder los 80 caracteres',
            'CLI_EMAIL.unique'   => 'Cliente ya registrado',

            'CLI_TELEFONO.required' => 'El teléfono del cliente es obligatorio',
            'CLI_TELEFONO.max'      => 'El teléfono no puede exceder los 15 caracteres',

            'CLI_DIRECCION.required' => 'La dirección del cliente es obligatoria',
            'CLI_DIRECCION.max'      => 'La dirección no puede exceder los 100 caracteres',

            'CLI_PASSWORD.required' => 'La contraseña es obligatoria',
            'CLI_PASSWORD.min'      => 'La contraseña debe tener al menos 6 caracteres',
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
