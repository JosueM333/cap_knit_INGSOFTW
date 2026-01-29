<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Carrito;
use App\Models\CarritoDetalle;
use App\Models\Bodega;
use App\Models\Transaccion;
use App\Models\Comprobante;
use App\Models\DetalleComprobante;
use App\Models\Kardex;
use Illuminate\Support\Str;
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
     * Paso 1: "Hacer Pedido"
     * Guarda el carrito de sesión en la BD y limpia la sesión.
     */
    public function saveOrder()
    {
        $cart = session()->get('cart', []);

        if (count($cart) <= 0) {
            return redirect()->route('shop.cart')->with('error', 'Tu carrito está vacío.');
        }

        $user = Auth::guard('cliente')->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para hacer un pedido.');
        }

        try {
            DB::connection('oracle_guayaquil')->beginTransaction();

            // 1. Crear Cabecera Carrito
            $carrito = new Carrito();
            $carrito->CLI_ID = $user->CLI_ID;
            $carrito->CRD_ESTADO = 'GUARDADO';

            // Calcular totales
            $subtotal = 0;
            foreach ($cart as $details) {
                $subtotal += ((float) $details['price']) * ((int) $details['quantity']);
            }
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            $carrito->CRD_SUBTOTAL = $subtotal;
            $carrito->CRD_IMPUESTO = $iva;
            $carrito->CRD_TOTAL = $total;
            $carrito->save();

            // 2. Crear Detalles (REQUERIDO: Se guardan al hacer el pedido)
            foreach ($cart as $proId => $details) {
                $detalle = new CarritoDetalle();
                $detalle->CRD_ID = $carrito->CRD_ID;
                $detalle->PRO_ID = (int) $proId;
                $detalle->DCA_CANTIDAD = (int) $details['quantity'];
                $detalle->DCA_PRECIO_UNITARIO = (float) $details['price'];
                $detalle->DCA_SUBTOTAL = ((float) $details['price']) * ((int) $details['quantity']);
                $detalle->save();
            }

            DB::connection('oracle_guayaquil')->commit();

            // Limpiar sesión
            session()->forget('cart');

            return redirect()->route('shop.pending')
                ->with('success', 'Pedido creado correctamente. Puedes revisarlo en tus Pedidos Pendientes.');

        } catch (Exception $e) {
            DB::connection('oracle_guayaquil')->rollBack();
            return redirect()->route('shop.cart')->with('error', 'Error al guardar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Vista de Pedidos Pendientes
     */
    public function pendingOrders()
    {
        $user = Auth::guard('cliente')->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $pedidos = Carrito::where('CLI_ID', $user->CLI_ID)
            ->where('CRD_ESTADO', 'GUARDADO')
            ->orderBy('created_at', 'desc')
            ->with(['detalles'])
            ->get();

        // Enriquecer con nombres de productos (BD1)
        $proIds = collect();
        foreach ($pedidos as $p) {
            $proIds = $proIds->merge($p->detalles->pluck('PRO_ID'));
        }
        $nombresProductos = Producto::whereIn('PRO_ID', $proIds->unique())->pluck('pro_nombre', 'pro_id');

        return view('shop.pending', compact('pedidos', 'nombresProductos'));
    }

    /**
     * Paso 2: "Facturar"
     * Procesa un pedido pendiente específico.
     */
    public function processOrder($id)
    {
        $user = Auth::guard('cliente')->user();

        // Buscar el carrito especifico
        $carritoBD = Carrito::where('CLI_ID', $user->CLI_ID)
            ->where('CRD_ID', $id)
            ->where('CRD_ESTADO', 'GUARDADO')
            ->with('detalles')
            ->firstOrFail();

        if ($carritoBD->detalles->isEmpty()) {
            return redirect()->back()->with('error', 'El pedido no tiene detalles.');
        }

        DB::connection('oracle')->beginTransaction();
        DB::connection('oracle_guayaquil')->beginTransaction();

        try {
            // 1. Configuración Bodega
            $bodegaDefecto = Bodega::where('BOD_ES_DEFECTO', 1)->first();
            if (!$bodegaDefecto)
                throw new Exception("No hay bodega por defecto.");
            $bodegaId = $bodegaDefecto->BOD_ID;
            $transaccionSalida = Transaccion::where('TRA_CODIGO', 'SALIDA')->firstOrFail();

            // 2. Crear Comprobante
            $comprobante = new Comprobante();
            $comprobante->CRD_ID = $carritoBD->CRD_ID;
            $comprobante->CLI_ID = $user->CLI_ID;
            $comprobante->BOD_ID = $bodegaId;
            $comprobante->COM_FECHA = now();
            $comprobante->COM_SUBTOTAL = $carritoBD->CRD_SUBTOTAL;
            $comprobante->COM_IVA = $carritoBD->CRD_IMPUESTO;
            $comprobante->COM_TOTAL = $carritoBD->CRD_TOTAL;
            $comprobante->COM_ESTADO = 'PAGADO';
            $comprobante->save();

            // 3. Procesar Stock y Kardex
            foreach ($carritoBD->detalles as $detalle) {
                $proId = $detalle->PRO_ID;
                $cantidad = (int) $detalle->DCA_CANTIDAD;

                // Lock Stock
                $bp = DB::connection('oracle')->table('BODEGA_PRODUCTO')
                    ->where('BOD_ID', $bodegaId)->where('PRO_ID', $proId)
                    ->lockForUpdate()->first();

                if (!$bp || $bp->bp_stock < $cantidad) {
                    throw new Exception("Stock insuficiente para el producto ID $proId.");
                }

                // Update Stock
                DB::connection('oracle')->table('BODEGA_PRODUCTO')
                    ->where('BOD_ID', $bodegaId)->where('PRO_ID', $proId)
                    ->update(['bp_stock' => $bp->bp_stock - $cantidad, 'updated_at' => now()]);

                // Detalle Comprobante
                $dc = new DetalleComprobante();
                $dc->COM_ID = $comprobante->COM_ID;
                $dc->PRO_ID = $proId;
                $dc->DCO_CANTIDAD = $cantidad;
                $dc->DCO_PRECIO_UNITARIO = $detalle->DCA_PRECIO_UNITARIO;
                $dc->DCO_SUBTOTAL = $detalle->DCA_SUBTOTAL;
                $dc->save();

                // Kardex
                Kardex::create([
                    'BOD_ID' => $bodegaId,
                    'PRO_ID' => $proId,
                    'TRA_ID' => $transaccionSalida->TRA_ID,
                    'COM_ID' => $comprobante->COM_ID,
                    'KRD_FECHA' => now(),
                    'KRD_CANTIDAD' => -1 * abs($cantidad),
                    'KRD_USUARIO' => $user->CLI_NOMBRES . ' ' . $user->CLI_APELLIDOS,
                    'KRD_OBSERVACION' => 'Venta Web #' . $comprobante->COM_ID
                ]);
            }

            // 4. Actualizar Carrito y Limpiar Detalles
            $carritoBD->CRD_ESTADO = 'FACTURADO';
            $carritoBD->save();

            // BORRAR DETALLES DE CARRITO (Requerimiento explícito)
            $carritoBD->detalles()->delete();

            DB::connection('oracle')->commit();
            DB::connection('oracle_guayaquil')->commit();

            return redirect()->route('shop.invoice', $comprobante->COM_ID)
                ->with('success', '¡Compra procesada con éxito!');

        } catch (Exception $e) {
            DB::connection('oracle')->rollBack();
            DB::connection('oracle_guayaquil')->rollBack();
            return redirect()->back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    public function invoice($id)
    {
        $comprobante = Comprobante::with(['cliente', 'detalles'])->findOrFail($id);

        // Seguridad: Solo el dueño puede ver su factura
        if (Auth::guard('cliente')->check() && $comprobante->CLI_ID != Auth::guard('cliente')->id()) {
            abort(403);
        }

        return view('shop.invoice', compact('comprobante'));
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