<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrden extends Model
{
    protected $table = 'DETALLE_ORDEN';
    protected $primaryKey = 'DOR_ID';
    public $timestamps = true;

    const CREATED_AT = 'DOR_CREATED_AT';
    const UPDATED_AT = 'DOR_UPDATED_AT';

    protected $fillable = [
        'ORD_ID',
        'PRO_ID',
        'DOR_CANTIDAD',
        'DOR_PRECIO',
        'DOR_SUBTOTAL'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }
}