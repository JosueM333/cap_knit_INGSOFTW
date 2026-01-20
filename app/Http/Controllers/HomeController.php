<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto; // Importamos el modelo para leer la BD

class HomeController extends Controller
{
    // ==========================================
    // VISTAS PÚBLICAS (TIENDA)
    // ==========================================

    /**
     * Página de Inicio (Landing Page)
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Catálogo de Productos
     * Muestra todos los productos activos y visibles de la BD.
     */
    public function products()
    {
        $productos = Producto::where('PRO_VISIBLE', 1)
                             ->where('PRO_ESTADO', 1)
                             ->get();

        return view('shop.products', compact('productos'));
    }

    /**
     * Detalle de un Producto Individual
     * Recibe el ID, busca en la BD y muestra la vista 'shop.show'.
     */
    public function show($id)
    {
        $producto = Producto::where('PRO_VISIBLE', 1)->findOrFail($id);
        return view('shop.show', compact('producto'));
    }

    /**
     * Vista del Carrito de Compras
     */
    public function cart()
    {
        return view('shop.cart');
    }

    /**
     * Vista de Contacto (Estática)
     */
    public function contact()
    {
        return view('shop.contact');
    }

    public function comprar()
    {
        // Caso conceptual: no hay pasarela de pago todavía

        // Validación mínima
        if (!session()->has('cart') || empty(session('cart'))) {
            return redirect()
                ->route('shop.cart')
                ->with('error', 'No hay productos en el carrito.');
        }

        // Limpiamos el carrito (simula compra exitosa)
        session()->forget('cart');

        // Mensaje conceptual de confirmación
        return redirect()
            ->route('shop.cart')
            ->with('success', 'Compra registrada correctamente. (Simulación)');
    }


    // ==========================================
    // LÓGICA DEL CARRITO (SESIÓN)
    // ==========================================

    /**
     * Agregar producto al carrito
     */
    public function addToCart($id)
    {
        $producto = Producto::findOrFail($id);
        
        // Obtenemos el carrito actual de la sesión
        $cart = session()->get('cart', []);

        // Si el producto ya está, aumentamos cantidad
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            // Si no está, lo agregamos con sus datos de la BD
            $cart[$id] = [
                "name" => $producto->PRO_NOMBRE,
                "quantity" => 1,
                "price" => $producto->PRO_PRECIO,
                // Si no tienes columna imagen, usamos una por defecto
                "image" => "static/img/gorra_default.jpg" 
            ];
        }

        // Guardamos el carrito actualizado en la sesión
        session()->put('cart', $cart);

        return redirect()->back()->with('success', '¡Producto añadido a la cesta correctamente!');
    }

    /**
     * Actualizar cantidad (Para AJAX en el futuro)
     */
    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {

            // Límite del flujo alterno (máx. 10)
            if ($request->quantity > 10) {
                return redirect()
                    ->route('shop.cart')
                    ->with('error', 'Máximo 10 unidades por producto.');
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


    /**
     * Eliminar del carrito (Para AJAX en el futuro)
     */
    public function removeCart(Request $request)
    {
        if ($request->id) {

            $cart = session()->get('cart', []);

            // E2: Producto no existe
            if (!isset($cart[$request->id])) {
                return redirect()
                    ->route('shop.cart')
                    ->with('error', 'El producto no existe en el carrito.');
            }

            unset($cart[$request->id]);

            // Si ya no quedan productos → carrito vacío
            if (empty($cart)) {
                session()->forget('cart');

                return redirect()
                    ->route('shop.cart')
                    ->with('success', 'Tu carrito está vacío.');
            }

            session()->put('cart', $cart);

            return redirect()
                ->route('shop.cart')
                ->with('success', 'Producto eliminado correctamente.');
        }

        // Seguridad por defecto
        return redirect()->route('shop.cart');
    }

    // ==========================================
    // ZONA PRIVADA (ADMIN)
    // ==========================================

    /**
     * Panel de Control (Dashboard)
     * Solo accesible si estás logueado
     */
    public function dashboard()
    {
        return view('home');
    }
}