<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Log;

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
        // CORRECCIÓN: Traer todos los proveedores (ya no existe PRV_ESTADO)
        $proveedores = Proveedor::all();
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
        } catch (\Exception $e) {
            Log::error("Error al crear producto: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al guardar el producto. Inténtelo de nuevo.')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $producto = Producto::obtenerProducto($id);

            if (!$producto) {
                return redirect()->route('productos.index')->with('error', 'El producto solicitado no existe.');
            }

            // CORRECCIÓN: Traer todos los proveedores
            $proveedores = Proveedor::all();

            return view('productos.edit', compact('producto', 'proveedores'));
        } catch (\Exception $e) {
            Log::error("Error al editar producto ($id): " . $e->getMessage());
            return redirect()->route('productos.index')->with('error', 'No se pudo cargar el formulario de edición.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::obtenerProducto($id);

            if (!$producto) {
                return redirect()->route('productos.index')->with('error', 'El producto que intenta actualizar no existe.');
            }

            $datos = $request->all();

            Producto::validar($datos, $id);
            $producto->actualizarProducto($datos);

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Error al actualizar producto ($id): " . $e->getMessage());
            return back()->with('error', 'Error al actualizar el producto. Verifique los datos e intente nuevamente.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $producto = Producto::obtenerProducto($id);

            if (!$producto) {
                return redirect()->route('productos.index')->with('error', 'El producto no existe o ya fue eliminado.');
            }

            // Borrado Físico
            $producto->eliminarProducto();

            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado del catálogo permanentemente.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar producto ($id): " . $e->getMessage());
            return redirect()->route('productos.index')->with('error', 'No se pudo eliminar el producto. Puede tener registros asociados (stock o historial).');
        }
    }
}