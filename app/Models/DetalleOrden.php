<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OracleCompatible;

class DetalleOrden extends Model
{
    use HasFactory, OracleCompatible;

    protected $connection = 'oracle';
    protected $table = 'DETALLE_ORDEN_COMPRA';
    protected $primaryKey = 'DOR_ID';

    // Al dejar esto en true sin definir constantes, Laravel usará 'created_at' y 'updated_at'
    // lo cual coincide perfectamente con tu migración corregida.
    public $timestamps = true;

    protected $fillable = [
        'ORD_ID',
        'PRO_ID',
        'DOR_CANTIDAD',
        'DOR_PRECIO',
        'DOR_SUBTOTAL'
    ];

    /* =========================================================
       RELACIONES
       ========================================================= */

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }

    // Relación inversa (opcional pero recomendada)
    public function orden()
    {
        return $this->belongsTo(OrdenCompra::class, 'ORD_ID', 'ORD_ID');
    }
}