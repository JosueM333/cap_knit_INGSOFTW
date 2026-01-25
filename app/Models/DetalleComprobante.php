<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OracleCompatible;

class DetalleComprobante extends Model
{
    use HasFactory, OracleCompatible;

    protected $table = 'DETALLE_COMPROBANTE';
    protected $primaryKey = 'DCO_ID';

    protected $keyType = 'int';
    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'COM_ID',
        'PRO_ID',
        'PRO_CODIGO_SNAP',
        'PRO_NOMBRE_SNAP',
        'DCO_CANTIDAD',
        'DCO_PRECIO_UNITARIO',
        'DCO_SUBTOTAL'
    ];

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'COM_ID', 'COM_ID');
    }
}
