<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'PRODUCTO';
    protected $primaryKey = 'PRO_ID';
    public $timestamps = true;

    const CREATED_AT = 'PRO_CREATED_AT';
    const UPDATED_AT = 'PRO_UPDATED_AT';

    protected $fillable = [
        'PRV_ID',
        'PRO_CODIGO',
        'PRO_NOMBRE',
        'PRO_DESCRIPCION',
        'PRO_PRECIO',
        'PRO_COLOR',
        'PRO_TALLA',
        'PRO_MARCA'
    ];

  
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'PRV_ID', 'PRV_ID');
    }

    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'BODEGA_PRODUCTO', 'PRO_ID', 'BOD_ID')
                    ->withPivot('BP_STOCK', 'BP_STOCK_MIN');
    }

    
   
     
    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'PRV_ID'          => 'required|exists:PROVEEDOR,PRV_ID',
            'PRO_CODIGO'      => 'required|string|max:20|unique:PRODUCTO,PRO_CODIGO' . ($id ? ",$id,PRO_ID" : ''),
            'PRO_NOMBRE'      => 'required|string|max:100',
            'PRO_DESCRIPCION' => 'required|string|max:500',
            'PRO_PRECIO'      => 'required|numeric|min:0.01',
            'PRO_MARCA'       => 'nullable|string|max:50',
            'PRO_COLOR'       => 'nullable|string|max:30',
        ];

        
        $mensajes = [
            'PRV_ID.required' => 'Datos de producto inconsistentes',
            'PRV_ID.exists'   => 'Datos de producto inconsistentes',

            'PRO_CODIGO.required' => 'Datos de producto inconsistentes',
            'PRO_CODIGO.unique'   => 'Datos de producto inconsistentes',
            'PRO_CODIGO.max'      => 'Datos de producto inconsistentes',

            'PRO_NOMBRE.required' => 'Datos de producto inconsistentes',
            'PRO_NOMBRE.max'      => 'Datos de producto inconsistentes',

            'PRO_DESCRIPCION.required' => 'Datos de producto inconsistentes',
            'PRO_DESCRIPCION.max'      => 'Datos de producto inconsistentes',

            'PRO_PRECIO.required' => 'Precio inválido',
            'PRO_PRECIO.numeric'  => 'Precio inválido',
            'PRO_PRECIO.min'      => 'Precio inválido',

            'PRO_MARCA.max' => 'Datos de producto inconsistentes',
            'PRO_COLOR.max' => 'Datos de producto inconsistentes',
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    

   
    public static function guardarProducto(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            $producto = self::create($datos);

            $bodegaDefecto = Bodega::first();
            
            if ($bodegaDefecto) {
                $producto->bodegas()->attach($bodegaDefecto->BOD_ID, [
                    'BP_STOCK' => 0,
                    'BP_STOCK_MIN' => 5
                ]);
            }

            return $producto;
        });
    }

    

     
    public static function obtenerProductos()
    {
        return self::with('proveedor')->get();
    }

    

     
    public static function buscarProducto($criterio)
    {
        return self::with('proveedor')
                   ->where('PRO_CODIGO', 'LIKE', "%$criterio%")
                   ->orWhere('PRO_NOMBRE', 'LIKE', "%$criterio%")
                   ->get();
    }

    

     
    public static function obtenerProducto($id)
    {
        return self::findOrFail($id);
    }

    

     
    public function actualizarProducto(array $datos)
    {
        $this->update($datos);
    }

    
    
     
    public function eliminarProducto()
    {
        $this->delete();
    }
}
