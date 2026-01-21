<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'PROVEEDOR';
    protected $primaryKey = 'PRV_ID';
    public $timestamps = true;

    const CREATED_AT = 'PRV_CREATED_AT';
    const UPDATED_AT = 'PRV_UPDATED_AT';

    protected $fillable = [
        'PRV_RUC',
        'PRV_NOMBRE',
        'PRV_TELEFONO',
        'PRV_EMAIL',
        'PRV_DIRECCION',
        'PRV_PERSONA_CONTACTO'
        
    ];


     
    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'PRV_RUC'      => 'required|string|size:13|unique:PROVEEDOR,PRV_RUC' . ($id ? ",$id,PRV_ID" : ''),
            'PRV_NOMBRE'   => 'required|string|max:100',
            'PRV_TELEFONO' => 'required|string|max:15',
            'PRV_EMAIL'    => 'required|email|max:80',
            'PRV_DIRECCION'=> 'required|string|max:100',
            'PRV_PERSONA_CONTACTO' => 'nullable|string|max:80',
        ];

         
        $mensajes = [
            'PRV_RUC.required' => 'El RUC del proveedor es obligatorio',
            'PRV_RUC.size'     => 'El RUC debe tener 13 dígitos',
            'PRV_RUC.unique'   => 'Proveedor ya registrado',

            'PRV_NOMBRE.required' => 'El nombre del proveedor es obligatorio',
            'PRV_NOMBRE.max'      => 'El nombre del proveedor no puede exceder los 100 caracteres',

            'PRV_TELEFONO.required' => 'El teléfono del proveedor es obligatorio',
            'PRV_TELEFONO.max'      => 'El teléfono no puede exceder los 15 caracteres',

            'PRV_EMAIL.required' => 'El correo electrónico es obligatorio',
            'PRV_EMAIL.email'    => 'El correo electrónico no tiene un formato válido',
            'PRV_EMAIL.max'      => 'El correo electrónico no puede exceder los 80 caracteres',

            'PRV_DIRECCION.required' => 'La dirección del proveedor es obligatoria',
            'PRV_DIRECCION.max'      => 'La dirección no puede exceder los 100 caracteres',

            'PRV_PERSONA_CONTACTO.max' => 'El nombre de la persona de contacto no puede exceder los 80 caracteres',
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
        $this->delete();
    }
}
