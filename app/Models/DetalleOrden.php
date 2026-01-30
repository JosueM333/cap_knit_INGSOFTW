<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    use HasFactory;

    protected $table = 'DETALLE_ORDEN';
    protected $primaryKey = 'DOR_ID';
    public $timestamps = false;

    protected $fillable = [
        'ORD_ID',
        'PRO_ID',
        'DOR_CANTIDAD',
        'DOR_PRECIO',
        'DOR_SUBTOTAL'
    ];

    // Relación con el Producto asociado al detalle
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }

    // Relación con la Orden de Compra cabecera
    public function orden()
    {
        return $this->belongsTo(OrdenCompra::class, 'ORD_ID', 'ORD_ID');
    }
}