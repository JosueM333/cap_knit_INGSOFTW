<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Traits\OracleCompatible;

class Proveedor extends Model
{
    use HasFactory, OracleCompatible;

    protected $connection = 'oracle';
    protected $table = 'PROVEEDOR';
    protected $primaryKey = 'PRV_ID';
    public $timestamps = true;



    protected $fillable = [
        'PRV_RUC',
        'PRV_NOMBRE',
        'PRV_TELEFONO',
        'PRV_EMAIL',
        'PRV_DIRECCION',
        'PRV_PERSONA_CONTACTO'
        // ELIMINADO: PRV_ESTADO
    ];

    /* =========================================================
       MÉTODOS DE LÓGICA DE NEGOCIO
       ========================================================= */

    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'PRV_RUC' => 'required|string|size:13|unique:PROVEEDOR,PRV_RUC' . ($id ? ",$id,PRV_ID" : ''),
            'PRV_NOMBRE' => 'required|string|max:100',
            'PRV_TELEFONO' => 'required|string|max:15',
            'PRV_EMAIL' => 'required|email|max:80',
            'PRV_DIRECCION' => 'required|string|max:100',
            'PRV_PERSONA_CONTACTO' => 'nullable|string|max:80',
        ];

        $mensajes = [
            'PRV_RUC.required' => 'El RUC del proveedor es obligatorio',
            'PRV_RUC.size' => 'El RUC debe tener 13 dígitos',
            'PRV_RUC.unique' => 'Este RUC ya está registrado',
            'PRV_EMAIL.email' => 'El formato del correo no es válido',
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function guardarProveedor(array $datos)
    {
        return self::create($datos);
    }

    public static function obtenerProveedores()
    {
        return self::all();
    }

    public static function buscarProveedor($criterio)
    {
        return self::where('PRV_RUC', 'LIKE', "%$criterio%")
            ->orWhere('PRV_NOMBRE', 'LIKE', "%$criterio%")
            ->get();
    }

    public static function obtenerProveedor($id)
    {
        return self::findOrFail($id);
    }

    public function actualizarProveedor(array $datos)
    {
        $this->update($datos);
    }

    public function eliminarProveedor()
    {
        // Lógica de Borrado Físico
        $this->delete();
    }
}