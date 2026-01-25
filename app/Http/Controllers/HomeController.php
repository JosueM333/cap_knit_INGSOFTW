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

        // Detectar ID del cliente
        $cli_id = null;

        if (Auth::guard('cliente')->check()) {
            $cli_id = Auth::guard('cliente')->user()->CLI_ID;
        } elseif (Auth::guard('web')->check()) {
            $cli_id = 1; // para pruebas (asegúrate que exista)
        } else {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para comprar.');
        }

        DB::beginTransaction();

        try {
            // Totales
            $subtotal = 0;
            foreach ($cart as $id => $details) {
                $subtotal += ((float) $details['price']) * ((int) $details['quantity']);
            }
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            // 1) Obtener CRD_ID desde SEQUENCE (sin RETURNING, funciona con DBLINK + sinónimo)
            $nextCarrito = DB::selectOne('SELECT CARRITO_SEQ.NEXTVAL AS ID FROM DUAL');
            $carritoId = (int) $nextCarrito->id;

            // 2) Insertar cabecera CARRITO (NO uses CRD_FECHA_CREACION si tu tabla ya no la tiene)
            $carritoBD = new Carrito();
            $carritoBD->CRD_ID = $carritoId;
            $carritoBD->CLI_ID = $cli_id;
            $carritoBD->CRD_ESTADO = 'GUARDADO';
            $carritoBD->CRD_SUBTOTAL = $subtotal;
            $carritoBD->CRD_IMPUESTO = $iva;
            $carritoBD->CRD_TOTAL = $total;
            $carritoBD->save();

            // 3) Insertar detalle DETALLE_CARRITO
            foreach ($cart as $proId => $details) {

                // Si DETALLE_CARRITO vive en BD2 por DBLINK, evita RETURNING también:
                $nextDetalle = DB::selectOne('SELECT DETALLE_CARRITO_SEQ.NEXTVAL AS ID FROM DUAL');
                $detalleId = (int) $nextDetalle->id;

                $detalle = new CarritoDetalle();
                $detalle->DCA_ID = $detalleId;
                $detalle->CRD_ID = $carritoId;
                $detalle->PRO_ID = (int) $proId;

                // Snapshots
                $detalle->PRO_CODIGO = $details['code'] ?? 'N/A';
                $detalle->PRO_NOMBRE = $details['name'];

                $detalle->DCA_CANTIDAD = (int) $details['quantity'];
                $detalle->DCA_PRECIO_UNITARIO = (float) $details['price'];
                $detalle->DCA_SUBTOTAL = ((float) $details['price']) * ((int) $details['quantity']);

                $detalle->save();
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->route('shop.index')
                ->with('success', '¡Compra #' . $carritoId . ' realizada con éxito!');

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