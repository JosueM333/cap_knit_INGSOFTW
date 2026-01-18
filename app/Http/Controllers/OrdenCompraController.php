<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class OrdenCompraController extends Controller
{
    /**
     * F2.2 Consultar y F2.3 Buscar
     */
    public function index(Request $request)
    {
        try {
            $criterio = $request->input('criterio');

            if ($criterio) {
                // F2.3: Ejecuta búsqueda filtrada
                $ordenes = OrdenCompra::buscar($criterio);

                // F2.3 Flujo Alterno: Mensaje de error si no encuentra nada
                if ($ordenes->isEmpty()) {
                    // CORRECCIÓN IMPORTANTE: 
                    // No redireccionamos (return redirect) porque eso borraba la búsqueda.
                    // Usamos flash para mostrar el mensaje y dejamos que la vista cargue vacía.
                    session()->flash('error', 'Orden de compra no localizada.');
                }
            } else {
                // F2.2: Consulta general (Solo si no hay búsqueda)
                $ordenes = OrdenCompra::with('proveedor')->orderBy('ORD_ID', 'desc')->get();
            }

            return view('ordenes.index', compact('ordenes'));

        } catch (QueryException $e) {
            // E1: No hay conexión con la base de datos.
            return redirect()->route('home')
                             ->with('error', 'E1: No hay conexión con la base de datos.');
        }
    }

    /**
     * F2.1: Mostrar Formulario Crear
     */
    public function create()
    {
        $proveedores = Proveedor::where('PRV_ESTADO', 1)->get();
        $productos = Producto::where('PRO_ESTADO', 1)->get();
        return view('ordenes.create', compact('proveedores', 'productos'));
    }

    /**
     * F2.1: Guardar
     */
    public function store(Request $request)
    {
        try {
            $datos = $request->all();
            OrdenCompra::validar($datos);
            OrdenCompra::guardarOrden($datos);

            return redirect()->route('ordenes.index')
                             ->with('success', 'Orden de Compra generada exitosamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (QueryException $e) {
            return back()->with('error', 'E1/E3: Error de base de datos.')->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Error inesperado: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Ver Detalle (Usado por F2.4 y F2.5 para localizar)
     */
    public function show($id)
    {
        $orden = OrdenCompra::with(['proveedor', 'detalles.producto'])->findOrFail($id);
        return view('ordenes.show', compact('orden'));
    }

    /**
     * F2.4: Mostrar Formulario de Edición
     */
    public function edit($id)
    {
        $orden = OrdenCompra::with(['detalles'])->findOrFail($id);

        // F2.4 Restricción: Solo editar si está Pendiente
        if ($orden->ORD_ESTADO !== 'P') {
            return redirect()->route('ordenes.index')
                             ->with('error', 'Orden no editable: Ya procesada.');
        }

        $proveedores = Proveedor::where('PRV_ESTADO', 1)->get();
        $productos = Producto::where('PRO_ESTADO', 1)->get();

        return view('ordenes.edit', compact('orden', 'proveedores', 'productos'));
    }

    /**
     * F2.4: Procesar Actualización
     */
    public function update(Request $request, $id)
    {
        try {
            $orden = OrdenCompra::findOrFail($id);

            // F2.4 Flujo Alterno: Validar estado antes de guardar
            if ($orden->ORD_ESTADO !== 'P') {
                return redirect()->route('ordenes.index')
                                 ->with('error', 'Orden no editable: Ya procesada.');
            }

            $datos = $request->all();
            OrdenCompra::validar($datos); // Reutilizamos la validación de F2.1
            $orden->actualizarOrdenCompleta($datos);

            return redirect()->route('ordenes.index')
                             ->with('success', 'Orden actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (QueryException $e) {
            return back()->with('error', 'E1/E3: Error de actualización o integridad.');
        }
    }

    /**
     * F2.5: Anular (Borrado Lógico)
     */
    public function destroy($id)
    {
        try {
            $orden = OrdenCompra::findOrFail($id);
            $orden->anular(); // Cambia estado a 'C'

            return redirect()->route('ordenes.index')
                             ->with('success', 'Orden anulada correctamente.');

        } catch (QueryException $e) {
            return back()->with('error', 'E1: Error al anular el registro.');
        }
    }
}