<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Comprobante;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Exception;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
    }

    public function index()
    {
        return view('welcome');
    }

    // Listado general de productos
    public function products()
    {
        $productos = Producto::all();
        return view('shop.products', compact('productos'));
    }

    public function show($id)
    {
        return view('shop.show', ['producto' => Producto::findOrFail($id)]);
    }

    // Sincroniza la sesión del carrito con los datos actuales de la BDD
    public function cart()
    {
        $cart = session()->get('cart', []);
        if (count($cart) > 0) {
            $productos = Producto::whereIn('PRO_ID', array_keys($cart))->get();
            foreach ($productos as $producto) {
                if (isset($cart[$producto->PRO_ID])) {
                    $cart[$producto->PRO_ID]['name'] = $producto->PRO_NOMBRE;
                    $cart[$producto->PRO_ID]['price'] = $producto->PRO_PRECIO;
                    $cart[$producto->PRO_ID]['code'] = $producto->PRO_CODIGO;
                    $cart[$producto->PRO_ID]['image'] = "img/productos/{$producto->PRO_CODIGO}.jpg";
                }
            }
            session()->put('cart', $cart);
        }
        return view('shop.cart');
    }

    public function contact()
    {
        return view('shop.contact');
    }

    // Muestra la vista de Checkout (Pasarela Simulada)
    public function checkout()
    {
        $cart = session()->get('cart');
        if (!$cart)
            return redirect()->route('shop.cart')->with('error', 'Cesta vacía.');

        $subtotal = 0;
        foreach ($cart as $details)
            $subtotal += $details['price'] * $details['quantity'];

        $iva = $subtotal * config('shop.iva');
        $total = number_format($subtotal + $iva, 2);

        return view('shop.checkout', compact('cart', 'total'));
    }

    // Procesa el pago simulado y genera la factura
    public function processPayment(Request $request)
    {
        $cart = session()->get('cart');
        if (!$cart)
            return redirect()->route('shop.cart')->with('error', 'Cesta vacía.');

        $cli_id = Auth::guard('cliente')->check() ? Auth::guard('cliente')->user()->CLI_ID : (Auth::check() ? 1 : null);
        if (!$cli_id)
            return redirect()->route('login')->with('error', 'Inicie sesión para pagar.');

        // Validación simple de datos de tarjeta "Fake"
        $request->validate([
            'paymentMethod' => 'required',
            'cc_number' => 'required',
            'cc_name' => 'required'
        ]);

        $metodoPago = $request->paymentMethod === 'paypal' ? 'PayPal' : 'Tarjeta';

        $saveCard = $request->has('save_card') ? " (Guardar Tarjeta: SÍ)" : "";

        $observacionPago = "Pagado con {$metodoPago} - Ref: " . substr($request->cc_number, -4) . " (Simulado)" . $saveCard;

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cart as $details)
                $subtotal += $details['price'] * $details['quantity'];
            $iva = $subtotal * config('shop.iva');
            $total = $subtotal + $iva;

            $carritoBD = Carrito::create([
                'CLI_ID' => $cli_id,
                'CRD_FECHA_CREACION' => now(),
                'CRD_ESTADO' => 'FACTURADA',
                'CRD_SUBTOTAL' => $subtotal,
                'CRD_IMPUESTO' => $iva,
                'CRD_TOTAL' => $total
            ]);

            foreach ($cart as $id => $details) {
                CarritoDetalle::create([
                    'CRD_ID' => $carritoBD->CRD_ID,
                    'PRO_ID' => $id,
                    'DCA_CANTIDAD' => $details['quantity'],
                    'DCA_PRECIO_UNITARIO' => $details['price'],
                    'DCA_SUBTOTAL' => $details['price'] * $details['quantity']
                ]);
            }

            $comprobante = Comprobante::create([
                'CRD_ID' => $carritoBD->CRD_ID,
                'CLI_ID' => $cli_id,
                'COM_FECHA' => now(),
                'COM_SUBTOTAL' => $subtotal,
                'COM_IVA' => $iva,
                'COM_TOTAL' => $total,
                'COM_OBSERVACIONES' => $observacionPago, // Aquí guardamos el detalle del pago
                'COM_ESTADO' => 'EMITIDO'
            ]);

            DB::commit();
            session()->forget('cart');

            // Limpiar también el carrito persistente activo si existe (ya que se acaba de facturar uno nuevo)
            // Nota: En un sistema real usaríamos el MISMO carrito, aquí creamos uno nuevo facturado y dejamos el activo "sucio"
            // Mejor práctica: Borrar items del carrito ACTIVO del usuario para que quede limpio
            $activeCart = Carrito::where('CLI_ID', $cli_id)->where('CRD_ESTADO', 'ACTIVO')->first();
            if ($activeCart) {
                $activeCart->vaciar(); // O borrarlo, como prefieras. vaciar() lo pone en VACIADO.
            }

            return redirect()->route('shop.invoice', $comprobante->COM_ID)->with('success', 'Pago exitoso. Compra completada.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error en pago: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al procesar el pago.');
        }
    }

    // Gestión de items en la sesión del carrito
    public function addToCart($id)
    {
        $producto = Producto::findOrFail($id);
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $producto->PRO_NOMBRE,
                "quantity" => 1,
                "price" => $producto->PRO_PRECIO,
                "code" => $producto->PRO_CODIGO,
                "image" => "img/productos/{$producto->PRO_CODIGO}.jpg"
            ];
        }

        session()->put('cart', $cart);

        // Persistencia
        if (Auth::guard('cliente')->check()) {
            $user = Auth::guard('cliente')->user();
            $carrito = Carrito::firstOrCreate(
                ['CLI_ID' => $user->CLI_ID, 'CRD_ESTADO' => 'ACTIVO'],
                ['CRD_FECHA_CREACION' => now(), 'CRD_SUBTOTAL' => 0, 'CRD_IMPUESTO' => 0, 'CRD_TOTAL' => 0]
            );

            // Update or Create Detalle
            $detalle = CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)->where('PRO_ID', $id)->first();
            if ($detalle) {
                $detalle->update([
                    'DCA_CANTIDAD' => $cart[$id]['quantity'],
                    'DCA_SUBTOTAL' => $cart[$id]['price'] * $cart[$id]['quantity']
                ]);
            } else {
                CarritoDetalle::create([
                    'CRD_ID' => $carrito->CRD_ID,
                    'PRO_ID' => $id,
                    'DCA_CANTIDAD' => $cart[$id]['quantity'],
                    'DCA_PRECIO_UNITARIO' => $cart[$id]['price'],
                    'DCA_SUBTOTAL' => $cart[$id]['price'] * $cart[$id]['quantity']
                ]);
            }
            $carrito->recalcularTotales();
        }

        return redirect()->back()->with('success', 'Producto añadido.');
    }

    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            $qty = max(1, min(10, (int) $request->quantity));
            $cart = session()->get('cart', []);

            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = $qty;
                session()->put('cart', $cart);

                // Persistencia si está logueado
                if (Auth::guard('cliente')->check()) {
                    $user = Auth::guard('cliente')->user();
                    // Si existe carrito activo, actualizar detalle
                    $carrito = Carrito::where('CLI_ID', $user->CLI_ID)
                        ->whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
                        ->first();
                    if ($carrito) {
                        $detalle = CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)
                            ->where('PRO_ID', $request->id)
                            ->first();
                        if ($detalle) {
                            $detalle->update([
                                'DCA_CANTIDAD' => $qty,
                                'DCA_SUBTOTAL' => $cart[$request->id]['price'] * $qty
                            ]);
                            $carrito->recalcularTotales();
                        }
                    }
                }

                if ($request->wantsJson()) {
                    // Cálculo de totales
                    $subtotal = array_reduce($cart, function ($i, $obj) {
                        return $i + ($obj['price'] * $obj['quantity']);
                    }, 0);
                    $iva = $subtotal * config('shop.iva');
                    $total = $subtotal + $iva;

                    return response()->json([
                        'success' => true,
                        'item_subtotal' => number_format($cart[$request->id]['price'] * $qty, 2),
                        'cart_subtotal' => number_format($subtotal, 2),
                        'cart_iva' => number_format($iva, 2),
                        'cart_total' => number_format($total, 2),
                        'cart_count' => count($cart)
                    ]);
                }
            }
        }
        return redirect()->route('shop.cart');
    }

    public function removeCart(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart', []);

            // Persistencia: Borrar de BDD
            if (Auth::guard('cliente')->check()) {
                $user = Auth::guard('cliente')->user();
                $carrito = Carrito::where('CLI_ID', $user->CLI_ID)
                    ->whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
                    ->first();
                if ($carrito) {
                    CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)
                        ->where('PRO_ID', $request->id)
                        ->delete();
                    $carrito->recalcularTotales();
                }
            }

            unset($cart[$request->id]);
            empty($cart) ? session()->forget('cart') : session()->put('cart', $cart);
        }
        return redirect()->route('shop.cart');
    }

    public function dashboard()
    {
        return view('home');
    }

    // Muestra el comprobante final con seguridad por ID de cliente
    public function invoice($id)
    {
        $comprobante = Comprobante::with(['cliente', 'carrito.detalles.producto'])->findOrFail($id);
        if (Auth::guard('cliente')->check() && $comprobante->CLI_ID !== Auth::guard('cliente')->user()->CLI_ID) {
            return redirect()->route('shop.index')->with('error', 'Acceso denegado.');
        }
        return view('shop.invoice', compact('comprobante'));
    }
    // Historial de compras del cliente autenticado
    public function myPurchases()
    {
        $cli_id = Auth::guard('cliente')->user()->CLI_ID ?? null;

        if (!$cli_id) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para ver sus compras.');
        }

        $comprobantes = Comprobante::where('CLI_ID', $cli_id)
            ->where('COM_ESTADO', '!=', 'ANULADO') // Opcional: Mostrar anulados o no
            ->orderBy('COM_FECHA', 'desc')
            ->get();

        return view('shop.purchases', compact('comprobantes'));
    }
}