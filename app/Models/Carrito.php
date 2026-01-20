<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'CARRITO';
    protected $primaryKey = 'CRD_ID';
    public $timestamps = true;

    protected $fillable = [
        'CLI_ID',
        'CRD_FECHA_CREACION',
        'CRD_ESTADO',
        'CRD_SUBTOTAL',
        'CRD_IMPUESTO',
        'CRD_TOTAL'
    ];

    /* =========================
       RELACIONES
       ========================= */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }

    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'CRD_ID', 'CRD_ID');
    }

    /* =========================
       VALIDACIONES
       ========================= */

    public static function validarCreacion(array $datos)
    {
        $validator = Validator::make($datos, [
            'CLI_ID' => 'required|exists:CLIENTE,CLI_ID',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /* =========================
       CASO F7.1 – Crear carrito
       ========================= */

    public static function crearCarrito(array $data)
    {
        return self::create([
            'CLI_ID' => $data['CLI_ID'],
            'CRD_ESTADO' => 'ACTIVO',
            'CRD_SUBTOTAL' => 0,
            'CRD_IMPUESTO' => 0,
            'CRD_TOTAL' => 0,
        ]);
    }

    /* =========================
       CASO F7.2 – Consultar Carritos
       ========================= */

    public static function obtenerCarritosActivos()
    {
        return self::whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
            ->with('cliente')
            ->orderBy('CRD_ID', 'DESC')
            ->get();
    }

    /* =========================
       CASO F7.3 – Buscar carrito por cliente
       ========================= */

    public static function buscarPorCliente(string $criterio)
    {
        return self::whereHas('cliente', function ($q) use ($criterio) {
            $q->where('CLI_CEDULA', 'LIKE', "%{$criterio}%")
            ->orWhere('CLI_EMAIL', 'LIKE', "%{$criterio}%");
        })
        ->whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
        ->with(['cliente', 'detalles.producto'])
        ->get();
    }

    /* =========================
       CASO F7.4 – Recalcular totales
       ========================= */

    public function recalcularTotales()
    {
        $subtotal = $this->detalles->sum(function ($item) {
            return $item->DCA_CANTIDAD * $item->DCA_PRECIO_UNITARIO;
        });

        $impuesto = $subtotal * 0.12; 
        $total    = $subtotal + $impuesto;

        $this->update([
            'CRD_SUBTOTAL' => $subtotal,
            'CRD_IMPUESTO' => $impuesto,
            'CRD_TOTAL'    => $total
        ]);
    }

    /* =========================
       CASO F7.5 – Vaciar carrito
       ========================= */

    public function vaciar()
    {
        $this->detalles()->delete();

        $this->update([
            'CRD_ESTADO'    => 'VACIADO',
            'CRD_SUBTOTAL' => 0,
            'CRD_IMPUESTO' => 0,
            'CRD_TOTAL'    => 0
        ]);
    }
}