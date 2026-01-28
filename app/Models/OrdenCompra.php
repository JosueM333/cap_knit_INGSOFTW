<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Traits\OracleCompatible;

class OrdenCompra extends Model
{
    use OracleCompatible;
    protected $connection = 'oracle';
    protected $table = 'ORDEN_COMPRA';
    protected $primaryKey = 'ORD_ID';
    public $timestamps = true;

    protected $fillable = [
        'PRV_ID',
        'BOD_ID', // Bodega destino
        'ORD_FECHA',
        'ORD_TOTAL',
        'ORD_ESTADO'
    ];

    // --- RELACIONES ---
    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'BOD_ID', 'BOD_ID');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'PRV_ID', 'PRV_ID');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleOrden::class, 'ORD_ID', 'ORD_ID');
    }

    // --- LÓGICA DE NEGOCIO ---

    public static function validar(array $datos)
    {
        $reglas = [
            'PRV_ID' => 'required|exists:PROVEEDOR,PRV_ID',
            'detalles' => 'required|array|min:1',
            'detalles.*.PRO_ID' => 'required|exists:PRODUCTO,PRO_ID',
            'detalles.*.CANTIDAD' => 'required|integer|min:1',
            'detalles.*.PRECIO' => 'required|numeric|min:0.01',
        ];

        $mensajes = [
            'PRV_ID.exists' => 'Error: El proveedor seleccionado no existe.',
            'detalles.required' => 'La orden debe tener al menos un producto.',
        ];

        $validator = Validator::make($datos, $reglas, $mensajes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function guardarOrden(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            $orden = self::create([
                'PRV_ID' => $datos['PRV_ID'],
                'ORD_FECHA' => now(),
                'ORD_TOTAL' => 0,
                'ORD_ESTADO' => 'PENDIENTE',
            ]);

            $totalGlobal = 0;

            foreach ($datos['detalles'] as $item) {
                $subtotal = $item['CANTIDAD'] * $item['PRECIO'];
                $totalGlobal += $subtotal;

                DetalleOrden::create([
                    'ORD_ID' => $orden->ORD_ID,
                    'PRO_ID' => $item['PRO_ID'],
                    'DOR_CANTIDAD' => $item['CANTIDAD'],
                    'DOR_PRECIO' => $item['PRECIO'],
                    'DOR_SUBTOTAL' => $subtotal
                ]);
            }

            $orden->update(['ORD_TOTAL' => $totalGlobal]);
            return $orden;
        });
    }

    public static function buscar($criterio)
    {
        return self::with('proveedor')
            ->where('ORD_ID', $criterio)
            ->orWhereHas('proveedor', function ($q) use ($criterio) {
                $q->where('PRV_NOMBRE', 'LIKE', "%$criterio%");
            })
            ->orderBy('ORD_ID', 'desc')
            ->get();
    }

    public function actualizarOrdenCompleta(array $datos)
    {
        return DB::transaction(function () use ($datos) {
            $this->update([
                'PRV_ID' => $datos['PRV_ID']
            ]);

            // Borrado Físico de detalles anteriores (Correcto para re-crear)
            $this->detalles()->delete();

            $totalGlobal = 0;
            foreach ($datos['detalles'] as $item) {
                $subtotal = $item['CANTIDAD'] * $item['PRECIO'];
                $totalGlobal += $subtotal;

                DetalleOrden::create([
                    'ORD_ID' => $this->ORD_ID,
                    'PRO_ID' => $item['PRO_ID'],
                    'DOR_CANTIDAD' => $item['CANTIDAD'],
                    'DOR_PRECIO' => $item['PRECIO'],
                    'DOR_SUBTOTAL' => $subtotal
                ]);
            }

            $this->update(['ORD_TOTAL' => $totalGlobal]);
        });
    }

    public function anular()
    {
        // Borrado Lógico (Cambio de Estado)
        $this->update(['ORD_ESTADO' => 'ANULADA']);
    }
}