<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CarritoDetalle extends Model
{
    use HasFactory;

    protected $table = 'CARRITO_DETALLE';
    protected $primaryKey = 'CDT_ID';
    public $timestamps = false;

    protected $fillable = [
        'CRD_ID',
        'PRO_ID',
        'CDT_CANTIDAD',
        'CDT_PRECIO_UNITARIO'
    ];

    /* =========================
       RELACIONES
       ========================= */

    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'CRD_ID', 'CRD_ID');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_ID', 'PRO_ID');
    }

    /* =========================
       VALIDACIONES
       ========================= */

    public static function validar(array $datos)
    {
        $validator = Validator::make($datos, [
            'CRD_ID'        => 'required|exists:CARRITO,CRD_ID',
            'PRO_ID'        => 'required|exists:PRODUCTO,PRO_ID',
            'CDT_CANTIDAD'  => 'required|integer|min:1',
            'CDT_PRECIO_UNITARIO' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function agregarProducto(Request $request, $id)
    {
        $request->validate([
            'PRO_ID'       => 'required|exists:PRODUCTO,PRO_ID',
            'DCA_CANTIDAD' => 'required|integer|min:1'
        ]);

        $carrito  = Carrito::findOrFail($id);
        $producto = Producto::findOrFail($request->PRO_ID);

        CarritoDetalle::updateOrCreate(
            [
                'CRD_ID' => $carrito->CRD_ID,
                'PRO_ID' => $producto->PRO_ID
            ],
            [
                'CDT_CANTIDAD'        => $request->DCA_CANTIDAD,
                'CDT_PRECIO_UNITARIO'=> $producto->PRO_PRECIO
            ]
        );

        $carrito->recalcularTotales();

        return redirect()
            ->route('carritos.editar', $carrito->CRD_ID)
            ->with('success', 'Producto agregado al carrito');
    }

}
