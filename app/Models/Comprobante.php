<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'COMPROBANTE';
    protected $primaryKey = 'COM_ID';
    public $timestamps = false;

    protected $fillable = [
        'CRD_ID',
        'CLI_ID',
        'COM_FECHA',
        'COM_SUBTOTAL',
        'COM_IVA',
        'COM_TOTAL',
        'COM_OBSERVACIONES',
        'COM_ESTADO'
    ];

    /* =========================================================
       RELACIONES
       ========================================================= */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'CRD_ID', 'CRD_ID');
    }

    /* =========================================================
       LOGICA DE NEGOCIO
       ========================================================= */

    public static function buscarPorCriterio($criterio)
    {
        return self::where('COM_ID', $criterio)
            ->orWhereHas('cliente', function ($query) use ($criterio) {
                $query->where('CLI_CEDULA', 'LIKE', "%$criterio%");
            })
            ->with('cliente')
            ->orderBy('COM_ID', 'desc')
            ->get();
    }
}