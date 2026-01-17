<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class DetalleCarrito extends Model
{
    use HasFactory;

    protected $table = 'DETALLE_CARRITO';
    protected $primaryKey = 'DCA_ID';
    public $timestamps = false;

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

    public static function validar(array $datos)
    {
        $reglas = [
            'CRD_ID'       => 'required|exists:CARRITO,CRD_ID',
            'PRO_ID'       => 'required|exists:PRODUCTO,PRO_ID',
            'DCA_CANTIDAD' => 'required|integer|min:1',
        ];

        $validator = Validator::make($datos, $reglas);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
