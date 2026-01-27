<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OracleCompatible;

class CarritoDetalle extends Model
{
    use HasFactory, OracleCompatible;

    protected $table = 'DETALLE_CARRITO';
    protected $primaryKey = 'DCA_ID';

    public $timestamps = true;

    protected $fillable = [
        'CRD_ID',
        'PRO_ID',
        'DCA_CANTIDAD',
        'DCA_PRECIO_UNITARIO',
        'DCA_SUBTOTAL'
    ];

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'CRD_ID', 'CRD_ID');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }
}