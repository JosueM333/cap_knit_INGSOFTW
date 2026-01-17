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
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cesta actualizada');
        }
    }

    /**
     * Eliminar del carrito (Para AJAX en el futuro)
     */
    public function removeCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Producto eliminado');
        }
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