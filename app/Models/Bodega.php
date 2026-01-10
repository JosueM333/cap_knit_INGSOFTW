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

    const CREATED_AT = 'BOD_CREATED_AT';
    const UPDATED_AT = 'BOD_UPDATED_AT';

    protected $fillable = [
        'BOD_NOMBRE',
        'BOD_UBICACION',
        'BOD_DESCRIPCION',
        'BOD_ESTADO'
    ];

    
    
     
    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'BOD_NOMBRE' => 'required|string|max:80|unique:BODEGA,BOD_NOMBRE' . ($id ? ",$id,BOD_ID" : ''),
            'BOD_UBICACION' => 'required|string|max:100',
            'BOD_DESCRIPCION' => 'nullable|string|max:200',
        ];

        
        $mensajes = [
            'BOD_NOMBRE.required' => 'El nombre de la bodega es obligatorio',
            'BOD_NOMBRE.unique'   => 'Bodega ya registrada',
            'BOD_NOMBRE.max'      => 'El nombre de la bodega no puede exceder los 80 caracteres',

            'BOD_UBICACION.required' => 'La ubicación de la bodega es obligatoria',
            'BOD_UBICACION.max'      => 'La ubicación no puede exceder los 100 caracteres',

            'BOD_DESCRIPCION.max' => 'La descripción no puede exceder los 200 caracteres',
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
        $this->delete();
    }
}
