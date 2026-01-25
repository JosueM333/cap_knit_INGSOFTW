<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            $iva = $subtotal * 0.15;
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
                    'PRO_CODIGO' => $details['code'] ?? 'N/A', // Snapshot
                    'PRO_NOMBRE' => $details['name'],           // Snapshot
                    'DCA_CANTIDAD' => $details['quantity'],
                    'DCA_PRECIO_UNITARIO' => $details['price'],
                    'DCA_SUBTOTAL' => $details['price'] * $details['quantity']
                ]);
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->route('shop.index')
                ->with('success', '¡Compra #' . $carritoBD->CRD_ID . ' realizada con éxito!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
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
                "code" => $producto->PRO_CODIGO, // Snapshot Code
                "quantity" => 1,
                "price" => $producto->PRO_PRECIO,
                "image" => "static/img/gorra_default.jpg"
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', '¡Producto añadido a la cesta correctamente!');
    }

    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            if ($request->quantity > 10) {
                return redirect()->route('shop.cart')->with('error', 'Máximo 10 unidades por producto.');
            }
            $cart = session()->get('cart', []);
            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = $request->quantity;
                session()->put('cart', $cart);
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
}