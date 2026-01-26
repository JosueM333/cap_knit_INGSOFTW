<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Comprobante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    /**
     * Constructor para proteger rutas.
     * SOLUCIÓN DEL BUCLE: Solo aplicamos bloqueo al dashboard.
     */
    public function __construct()
    {
        // Esto protege SOLO la ruta /home. 
        // El resto (/, /shop, etc.) queda libre para clientes y admins.
        $this->middleware('auth')->only('dashboard');
    }

    // ==========================================
    // ZONA PÚBLICA (TIENDA)
    // ==========================================

    public function index()
    {
        return view('welcome');
    }

    public function products()
    {
        // CORRECCIÓN: Eliminados filtros de columnas inexistentes (PRO_VISIBLE, PRO_ESTADO)
        // Ahora traemos todos los productos existentes físicamente.
        $productos = Producto::all();

        return view('shop.products', compact('productos'));
    }

    public function show($id)
    {
        // CORRECCIÓN: Eliminado filtro PRO_VISIBLE
        $producto = Producto::findOrFail($id);
        return view('shop.show', compact('producto'));
    }

    public function cart()
    {
        // SELF-HEALING: Actualizar datos del carrito al entrar (precios, códigos, imágenes)
        $cart = session()->get('cart', []);

        if (count($cart) > 0) {
            $ids = array_keys($cart);
            $productos = Producto::whereIn('PRO_ID', $ids)->get();

            foreach ($productos as $producto) {
                if (isset($cart[$producto->PRO_ID])) {
                    $cart[$producto->PRO_ID]['name'] = $producto->PRO_NOMBRE;
                    $cart[$producto->PRO_ID]['price'] = $producto->PRO_PRECIO; // Actualizar precio si cambió
                    $cart[$producto->PRO_ID]['code'] = $producto->PRO_CODIGO;
                    $cart[$producto->PRO_ID]['image'] = "img/productos/" . $producto->PRO_CODIGO . ".jpg";
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

    /**
     * PROCESAR COMPRA REAL
     */
    public function comprar(Request $request)
    {
        $cart = session()->get('cart');

        if (!$cart || count($cart) <= 0) {
            return redirect()->route('shop.cart')->with('error', 'No hay productos en el carrito.');
        }

        // Detectar ID del cliente de forma segura
        $cli_id = null;

        if (Auth::guard('cliente')->check()) {
            // Es un cliente real
            $cli_id = Auth::guard('cliente')->user()->CLI_ID;
        } elseif (Auth::guard('web')->check()) {
            // Es un Admin probando la compra. 
            $cli_id = 1; // Asegúrate de que exista el Cliente ID 1 para pruebas
        } else {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para comprar.');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;
            foreach ($cart as $id => $details) {
                $subtotal += $details['price'] * $details['quantity'];
            }
            $iva = $subtotal * config('shop.iva');
            $total = $subtotal + $iva;

            $carritoBD = Carrito::create([
                'CLI_ID' => $cli_id,
                // CRD_FECHA_CREACION removido si usas timestamps estándar, 
                // pero si tu migración final lo conservó, déjalo aquí.
                // Basado en tu último modelo Carrito, usas timestamps false y CRD_FECHA_CREACION manual?
                // Si aplicaste mi corrección de Carrito, esto debería ser automático (created_at).
                // Pero lo dejo como lo tenías para evitar romper esa parte si no actualizaste el modelo.
                'CRD_FECHA_CREACION' => now(),
                'CRD_ESTADO' => 'GUARDADO',
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

            // --- AUTO-GENERACIÓN DE FACTURA (CLIENTE) ---
            $comprobante = new Comprobante();
            $comprobante->CRD_ID = $carritoBD->CRD_ID;
            $comprobante->CLI_ID = $cli_id;
            $comprobante->COM_FECHA = now();
            $comprobante->COM_SUBTOTAL = $subtotal;
            $comprobante->COM_IVA = $iva;
            $comprobante->COM_TOTAL = $total;
            $comprobante->COM_OBSERVACIONES = "Compra Online - Autogenerada";
            $comprobante->COM_ESTADO = 'EMITIDO';
            $comprobante->save();

            // Actualizar carrito a FACTURADA para cerrar el ciclo
            $carritoBD->CRD_ESTADO = 'FACTURADA';
            $carritoBD->save();

            DB::commit();
            session()->forget('cart');

            return redirect()->route('shop.invoice', $comprobante->COM_ID)
                ->with('success', '¡Gracias por tu compra! Tu factura ha sido generada.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error procesando compra (Cliente ID: $cli_id): " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error inesperado al procesar su compra. Por favor intente nuevamente.');
        }
    }

    // ==========================================
    // LÓGICA DEL CARRITO (SESIÓN)
    // ==========================================

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
                "code" => $producto->PRO_CODIGO, // Guardamos el código para referencias futuras
                "image" => "img/productos/" . $producto->PRO_CODIGO . ".jpg"
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', '¡Producto añadido a la cesta correctamente!');
    }

    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            $qty = (int) $request->quantity;

            if ($qty > 10) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Máximo 10 unidades por producto.'], 422);
                }
                return redirect()->route('shop.cart')->with('error', 'Máximo 10 unidades por producto.');
            }

            if ($qty < 1)
                $qty = 1;

            $cart = session()->get('cart', []);

            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = $qty;
                session()->put('cart', $cart);

                // Recalcular totales si es AJAX
                if ($request->wantsJson()) {
                    $itemSubtotal = $cart[$request->id]['price'] * $qty;

                    $total = 0;
                    foreach ($cart as $item) {
                        $total += $item['price'] * $item['quantity'];
                    }
                    $iva = $total * config('shop.iva');
                    $grandTotal = $total + $iva;

                    return response()->json([
                        'success' => true,
                        'message' => 'Cesta actualizada',
                        'item_subtotal' => number_format($itemSubtotal, 2),
                        'cart_subtotal' => number_format($total, 2),
                        'cart_iva' => number_format($iva, 2),
                        'cart_total' => number_format($grandTotal, 2),
                        'cart_count' => count($cart)
                    ]);
                }
            }

            session()->flash('success', 'Cesta actualizada');
        }
        return redirect()->route('shop.cart');
    }

    public function removeCart(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart', []);
            if (!isset($cart[$request->id])) {
                return redirect()->route('shop.cart')->with('error', 'El producto no existe.');
            }
            unset($cart[$request->id]);
            if (empty($cart)) {
                session()->forget('cart');
                return redirect()->route('shop.cart')->with('success', 'Tu carrito está vacío.');
            }
            session()->put('cart', $cart);
            return redirect()->route('shop.cart')->with('success', 'Producto eliminado.');
        }
        return redirect()->route('shop.cart');
    }

    // ==========================================
    // ZONA PRIVADA (ADMIN)
    // ==========================================

    public function dashboard()
    {
        return view('home');
    }

    public function invoice($id)
    {
        $comprobante = Comprobante::with(['cliente', 'carrito.detalles.producto'])->findOrFail($id);

        // Seguridad: Verificar que el comprobante pertenezca al usuario logueado
        // (Si es Admin permitimos ver todos, si es Cliente solo los suyos)
        if (Auth::guard('cliente')->check()) {
            if ($comprobante->CLI_ID !== Auth::guard('cliente')->user()->CLI_ID) {
                return redirect()->route('shop.index')->with('error', 'No tienes permiso para ver este comprobante.');
            }
        }

        return view('shop.invoice', compact('comprobante'));
    }
}