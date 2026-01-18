<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CarritoDetalle extends Model
{
    use HasFactory;

    // ⚠️ IMPORTANTE: Verifica que este sea el nombre EXACTO en tu BD
    // Si en tu base de datos es 'CARRITO_DETALLE', cambia la línea de abajo.
    protected $table = 'DETALLE_CARRITO'; 
    
    // Llave primaria de esta tabla
    protected $primaryKey = 'DCA_ID';
    
    // Desactivamos timestamps si tu tabla no tiene created_at/updated_at
    public $timestamps = false;

    // ⚠️ IMPORTANTE: Estos campos deben existir en la BD tal cual están escritos aquí
    protected $fillable = [
        'CRD_ID',
        'PRO_ID',
        'DCA_CANTIDAD',
        'DCA_PRECIO_UNITARIO',
        'DCA_SUBTOTAL'
    ];

    /* =========================
       RELACIONES
       ========================= */
    public function carrito()
    {
        // Relación inversa con Carrito
        return $this->belongsTo(Carrito::class, 'CRD_ID', 'CRD_ID');
    }

    public function producto()
    {
        // Relación con Producto
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }

    /* =========================
       VALIDACIÓN MANUAL (Opcional)
       ========================= */
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