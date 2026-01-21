<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Bodega extends Model
{
    use HasFactory;

    protected $table = 'BODEGA';
    protected $primaryKey = 'BOD_ID';
    public $timestamps = true;

    protected $fillable = [
        'BOD_NOMBRE',
        'BOD_UBICACION',
        'BOD_DESCRIPCION'
        // ELIMINADO: BOD_ESTADO
    ];

    /* =========================================================
       MÉTODOS DE LÓGICA DE NEGOCIO
       ========================================================= */

    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'BOD_NOMBRE'    => 'required|string|max:80|unique:BODEGA,BOD_NOMBRE' . ($id ? ",$id,BOD_ID" : ''),
            'BOD_UBICACION' => 'required|string|max:100',
            'BOD_DESCRIPCION' => 'nullable|string|max:200',
        ];

        $mensajes = [
            'BOD_NOMBRE.required' => 'El nombre de la bodega es obligatorio',
            'BOD_NOMBRE.unique'   => 'Ya existe una bodega con este nombre',
            'BOD_UBICACION.required' => 'La ubicación es obligatoria',
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function guardarBodega(array $datos)
    {
        return self::create($datos);
    }

    public static function obtenerBodegas()
    {
        return self::all();
    }

    public static function buscarBodega($criterio)
    {
        return self::where('BOD_NOMBRE', 'LIKE', "%$criterio%")
                   ->orWhere('BOD_UBICACION', 'LIKE', "%$criterio%")
                   ->get();
    }

    public static function obtenerBodega($id)
    {
        return self::findOrFail($id);
    }

    public function actualizarBodega(array $datos)
    {
        $this->update($datos);
    }

    public function eliminarBodega()
    {
        // Borrado Físico
        $this->delete();
    }
}