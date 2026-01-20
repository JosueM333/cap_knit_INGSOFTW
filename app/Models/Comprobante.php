<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory;

    // 1. Nombre de la tabla
    protected $table = 'COMPROBANTE';

    // 2. Llave primaria personalizada
    protected $primaryKey = 'COM_ID';

    // 3. Activamos timestamps
    public $timestamps = true;

    // 4. Definimos los nombres de las columnas de fecha
    const CREATED_AT = 'COM_CREATED_AT';
    const UPDATED_AT = 'COM_UPDATED_AT';

    // 5. Campos asignables
    protected $fillable = [
        'CRD_ID',           // ID del Carrito
        'CLI_ID',           // ID del Cliente
        'COM_FECHA',        // Fecha de Emisión
        'COM_SUBTOTAL',     
        'COM_IVA',          // Monto del IVA (15%)
        'COM_TOTAL',        // Monto Total
        'COM_OBSERVACIONES',
        'COM_ESTADO'        // Ej: 'EMITIDO'
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
       LOGICA DE NEGOCIO (CASO F5.3)
       ========================================================= */

    /**
     * Busca comprobantes por ID de factura O Cédula del cliente.
     */
    public static function buscarPorCriterio($criterio)
    {
        return self::where('COM_ID', $criterio)
                   ->orWhereHas('cliente', function ($query) use ($criterio) {
                       $query->where('CLI_CEDULA', $criterio);
                   })
                   ->with('cliente') // Optimización: Trae datos del cliente
                   ->get();
    }
}