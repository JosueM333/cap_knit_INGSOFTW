<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Traits\OracleCompatible;

class Carrito extends Model
{
    use HasFactory, OracleCompatible;

    protected $table = 'CARRITO';
    protected $primaryKey = 'CRD_ID';

    protected $keyType = 'int';
    public $incrementing = false;

    public $timestamps = true; // Usamos el estándar

    protected $fillable = [
        'CLI_ID',
        // ELIMINADO: CRD_FECHA_CREACION (Se usa created_at)
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
       LÓGICA DE NEGOCIO
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

    public static function obtenerCarritosActivos()
    {
        return self::whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
            ->with('cliente')
            ->orderBy('CRD_ID', 'DESC')
            ->get();
    }

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

    public function recalcularTotales()
    {
        $subtotal = $this->detalles->sum('DCA_SUBTOTAL'); // Optimizado
        $impuesto = $subtotal * 0.15;
        $total = $subtotal + $impuesto;

        $this->update([
            'CRD_SUBTOTAL' => $subtotal,
            'CRD_IMPUESTO' => $impuesto,
            'CRD_TOTAL' => $total
        ]);
    }

    public function vaciar()
    {
        // Borrado físico de los detalles
        $this->detalles()->delete();

        $this->update([
            'CRD_ESTADO' => 'VACIADO',
            'CRD_SUBTOTAL' => 0,
            'CRD_IMPUESTO' => 0,
            'CRD_TOTAL' => 0
        ]);
    }
}