<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'CARRITO';
    protected $primaryKey = 'CRD_ID';
    public $timestamps = false;

    protected $fillable = [
        'CLI_ID',
        'CRD_ESTADO',
        'CRD_SUBTOTAL',
        'CRD_IMPUESTO',
        'CRD_TOTAL'
    ];

    // Relación: El carrito pertenece a un Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_ID', 'CLI_ID');
    }

    // Relación: El carrito posee múltiples detalles de productos
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class, 'CRD_ID', 'CRD_ID');
    }

    // Valida la existencia del cliente antes de crear el carrito
    public static function validarCreacion(array $datos)
    {
        $validator = Validator::make($datos, [
            'CLI_ID' => 'required|exists:CLIENTE,CLI_ID',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    // Inicializa un nuevo carrito con valores en cero y estado ACTIVO
    public static function crearCarrito(array $data)
    {
        return self::create([
            'CLI_ID' => $data['CLI_ID'],
            'CRD_ESTADO' => 'ACTIVO',
            'CRD_SUBTOTAL' => 0,
            'CRD_IMPUESTO' => 0,
            'CRD_TOTAL' => 0,
        ]);
    }

    // Obtiene carritos que no han sido procesados o finalizados
    public static function obtenerCarritosActivos()
    {
        return self::whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
            ->with('cliente')
            ->orderBy('CRD_ID', 'DESC')
            ->get();
    }

    // Busca carritos activos filtrando por identificación del cliente
    public static function buscarPorCliente(string $criterio)
    {
        return self::whereHas('cliente', function ($q) use ($criterio) {
            $q->where('CLI_CEDULA', 'LIKE', "%{$criterio}%")
                ->orWhere('CLI_EMAIL', 'LIKE', "%{$criterio}%");
        })
            ->whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
            ->with(['cliente', 'detalles.producto'])
            ->get();
    }

    // Suma los detalles y aplica el cálculo de impuestos (Dynamic Config)
    public function recalcularTotales()
    {
        $subtotal = $this->detalles->sum('DCA_SUBTOTAL');
        $impuesto = $subtotal * config('shop.iva');
        $total = $subtotal + $impuesto;

        $this->update([
            'CRD_SUBTOTAL' => $subtotal,
            'CRD_IMPUESTO' => $impuesto,
            'CRD_TOTAL' => $total
        ]);
    }

    // Elimina los items del carrito y cambia su estado a VACIADO
    public function vaciar()
    {
        $this->detalles()->delete();

        $this->update([
            'CRD_ESTADO' => 'VACIADO',
            'CRD_SUBTOTAL' => 0,
            'CRD_IMPUESTO' => 0,
            'CRD_TOTAL' => 0
        ]);
    }

    /**
     * Sincroniza el carrito de la sesión con la base de datos al iniciar sesión.
     * Fusiona cantidades si el producto ya existe.
     */
    public static function syncSessionToDatabase($user)
    {
        $sessionCart = session()->get('cart', []);

        // 1. Obtener o crear carrito activo para el usuario
        $carrito = self::firstOrCreate(
            [
                'CLI_ID' => $user->CLI_ID,
                'CRD_ESTADO' => 'ACTIVO'
            ],
            [
                'CRD_FECHA_CREACION' => now(),
                'CRD_SUBTOTAL' => 0,
                'CRD_IMPUESTO' => 0,
                'CRD_TOTAL' => 0
            ]
        );

        // 2. Fusionar items de sesión en la BDD
        if (!empty($sessionCart)) {
            foreach ($sessionCart as $proId => $details) {
                $detalle = CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)
                    ->where('PRO_ID', $proId)
                    ->first();

                if ($detalle) {
                    // Si ya existe, sumar cantidad (opcional, aquí reemplazamos o sumamos)
                    // Estrategia: Sumar cantidades
                    $newQty = $detalle->DCA_CANTIDAD + $details['quantity'];
                    $detalle->update([
                        'DCA_CANTIDAD' => min(10, $newQty), // Limite de 10
                        'DCA_PRECIO_UNITARIO' => $details['price'],
                        'DCA_SUBTOTAL' => $details['price'] * min(10, $newQty)
                    ]);
                } else {
                    // Si no existe, crear
                    CarritoDetalle::create([
                        'CRD_ID' => $carrito->CRD_ID,
                        'PRO_ID' => $proId,
                        'DCA_CANTIDAD' => $details['quantity'],
                        'DCA_PRECIO_UNITARIO' => $details['price'],
                        'DCA_SUBTOTAL' => $details['price'] * $details['quantity']
                    ]);
                }
            }
            $carrito->recalcularTotales();
        }

        // 3. Recargar sesión desde BDD (Para traer items que estaban guardados previamente)
        $bddItems = $carrito->detalles()->with('producto')->get();
        $mergedCart = [];

        foreach ($bddItems as $item) {
            $mergedCart[$item->PRO_ID] = [
                "name" => $item->producto->PRO_NOMBRE,
                "quantity" => $item->DCA_CANTIDAD,
                "price" => $item->producto->PRO_PRECIO,
                "code" => $item->producto->PRO_CODIGO,
                "image" => "img/productos/{$item->producto->PRO_CODIGO}.jpg"
            ];
        }

        session()->put('cart', $mergedCart);
    }
}