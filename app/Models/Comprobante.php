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

    // 4. Definimos los nombres de las columnas de fecha para que Laravel las reconozca
    const CREATED_AT = 'COM_CREATED_AT';
    const UPDATED_AT = 'COM_UPDATED_AT';

    // 5. Campos que se pueden asignar masivamente (Mass Assignment)
    protected $fillable = [
        'CRD_ID',           // ID del Carrito (Venta Origen)
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

    /**
     * Relación con el Cliente.
     * Un comprobante pertenece a un cliente.
     */
    public function cliente()
    {
        // belongsTo(Modelo, 'Foreign_Key_Local', 'Owner_Key_Externa')
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }

    /**
     * Relación con el Carrito (Venta).
     * Un comprobante pertenece a un carrito (venta origen).
     */
    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'CRD_ID', 'CRD_ID');
    }
}