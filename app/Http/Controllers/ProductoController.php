<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor; 
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{

    public function index(Request $request)
    {
        $criterio = $request->input('search');

        if ($criterio) {
            
            $productos = Producto::buscarProducto($criterio);
        } else {
            
            $productos = Producto::obtenerProductos();
        }

        return view('productos.index', compact('productos'));
    }

    
    public function create()
    {
        
        $proveedores = Proveedor::where('PRV_ESTADO', 1)->get();
        return view('productos.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->all();

            Producto::validar($datos);
            
            Producto::guardarProducto($datos);

            return redirect()->route('productos.index')
                             ->with('success', 'Producto creado y asignado a bodega correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    
    public function edit($id)
    {
        
        $producto = Producto::obtenerProducto($id);
        $proveedores = Proveedor::where('PRV_ESTADO', 1)->get();
        
        return view('productos.edit', compact('producto', 'proveedores'));
    }


    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::obtenerProducto($id);
            $datos = $request->all();

            Producto::validar($datos, $id);
            $producto->actualizarProducto($datos);

            return redirect()->route('productos.index')
                             ->with('success', 'Producto actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

   

    
    public function destroy($id)
    {
        $producto = Producto::obtenerProducto($id);
        
        $producto->eliminarProducto();

        return redirect()->route('productos.index')
                         ->with('success', 'Producto eliminado del catálogo.');
    }
}