<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Traits\OracleCompatible;


class Producto extends Model
{
    use HasFactory, OracleCompatible;

    protected $connection = 'oracle';
    protected $table = 'PRODUCTO';
    protected $primaryKey = 'PRO_ID';
    public $timestamps = true;

    protected $fillable = [
        'PRV_ID',
        'PRO_CODIGO',
        'PRO_NOMBRE',
        'PRO_DESCRIPCION',
        'PRO_PRECIO',
        'PRO_COLOR',
        'PRO_TALLA',
        'PRO_MARCA'
        // ELIMINADOS: PRO_ESTADO, PRO_VISIBLE
    ];

    /* =========================================================
       RELACIONES
       ========================================================= */

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'PRV_ID', 'PRV_ID');
    }

    public function bodegas()
    {
        return $this->belongsToMany(Bodega::class, 'BODEGA_PRODUCTO', 'PRO_ID', 'BOD_ID')
            ->withPivot('BP_STOCK', 'BP_STOCK_MIN')
            ->withTimestamps(); // Maneja automáticamente los timestamps de la pivote
    }

    /* =========================================================
       LÓGICA DE NEGOCIO
       ========================================================= */

    public static function validar(array $datos, $id = null)
    {
        $reglas = [
            'PRV_ID' => 'required|exists:PROVEEDOR,PRV_ID',
            'PRO_CODIGO' => 'required|string|max:20|unique:PRODUCTO,PRO_CODIGO' . ($id ? ",$id,PRO_ID" : ''),
            'PRO_NOMBRE' => 'required|string|max:100',
            'PRO_DESCRIPCION' => 'required|string|max:500',
            'PRO_PRECIO' => 'required|numeric|min:0.01',
            'PRO_MARCA' => 'nullable|string|max:50',
            'PRO_COLOR' => 'nullable|string|max:30',
            // Agregado para consistencia (estaba en fillable pero no validado)
            'PRO_TALLA' => 'nullable|string|max:10',
        ];

        $mensajes = [
            'PRV_ID.required' => 'Debe seleccionar un proveedor',
            'PRO_CODIGO.unique' => 'El código del producto ya existe',
            'PRO_PRECIO.min' => 'El precio debe ser mayor a 0',
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
        // Borrado Físico
        $this->delete();
    }



}