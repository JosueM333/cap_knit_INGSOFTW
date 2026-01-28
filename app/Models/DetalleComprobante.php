<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OracleCompatible;

class DetalleComprobante extends Model
{
    use HasFactory, OracleCompatible;

    protected $connection = 'oracle_guayaquil';
    protected $table = 'DETALLE_COMPROBANTE';
    protected $primaryKey = 'DCO_ID';



    public $timestamps = true;

    protected $fillable = [
        'COM_ID',
        'PRO_ID',
        'DCO_CANTIDAD',
        'DCO_PRECIO_UNITARIO',
        'DCO_SUBTOTAL'
    ];

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'COM_ID', 'COM_ID');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }
}
