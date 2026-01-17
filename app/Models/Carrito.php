<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'CARRITO';
    protected $primaryKey = 'CRD_ID';
    public $timestamps = false;

    protected $fillable = [
        'CLI_ID',
        'CRD_FECHA_CREACION',
        'CRD_ESTADO',
        'CRD_SUBTOTAL',
        'CRD_IMPUESTO',
        'CRD_TOTAL'
    ];

    /* ================= RELACIONES ================= */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }

    /* ================= VALIDACIÓN ================= */

    public static function validar(array $datos)
    {
        $validator = Validator::make($datos, [
            'CLI_ID'     => 'required|exists:CLIENTE,CLI_ID',
            'CRD_ESTADO' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /* ================= LÓGICA ================= */

    public static function crearCarrito(array $datos)
    {
        $datos['CRD_FECHA_CREACION'] = now();
        $datos['CRD_SUBTOTAL'] = 0;
        $datos['CRD_IMPUESTO'] = 0;
        $datos['CRD_TOTAL'] = 0;

        return self::create($datos);
    }

    public static function obtenerCarritosActivos()
    {
        return self::where('CRD_ESTADO', 'ACTIVO')
            ->with('cliente')
            ->get();
    }

    public static function buscarPorCliente($criterio)
    {
        return self::whereHas('cliente', function ($q) use ($criterio) {
            $q->where('CLI_CEDULA', 'LIKE', "%$criterio%")
              ->orWhere('CLI_EMAIL', 'LIKE', "%$criterio%");
        })
        ->where('CRD_ESTADO', 'ACTIVO')
        ->with('cliente')
        ->get();
    }

    public function vaciar()
    {
        $this->CRD_ESTADO = 'VACIADO';
        $this->CRD_SUBTOTAL = 0;
        $this->CRD_IMPUESTO = 0;
        $this->CRD_TOTAL = 0;
        $this->save();
    }
}

