<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\OracleCompatible;

class Kardex extends Model
{
    use HasFactory, OracleCompatible;

    protected $connection = 'oracle';
    protected $table = 'KARDEX';
    protected $primaryKey = 'KRD_ID';
    public $timestamps = true;

    protected $fillable = [
        'BOD_ID',
        'PRO_ID',
        'TRA_ID',
        'ORD_ID',
        'COM_ID',
        'KRD_FECHA',
        'KRD_CANTIDAD',
        'KRD_SALDO',
        'KRD_USUARIO',
        'KRD_OBSERVACION'
    ];

    // RELACIONES

    public function bodega()
    {
        return $this->belongsTo(Bodega::class, 'BOD_ID', 'BOD_ID');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }

    public function transaccion()
    {
        return $this->belongsTo(Transaccion::class, 'TRA_ID', 'TRA_ID');
    }

    public function ordenCompra()
    {
        return $this->belongsTo(OrdenCompra::class, 'ORD_ID', 'ORD_ID');
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class, 'COM_ID', 'COM_ID');
    }
}
