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
    public function index(Request $request)
    {
        try {
            $criterio = $request->input('criterio');

            if ($criterio) {
                $ordenes = OrdenCompra::buscar($criterio);

                if ($ordenes->isEmpty()) {
                    session()->flash('error', 'Orden de compra no localizada.');
                }
            } else {
                $ordenes = OrdenCompra::with('proveedor')->orderBy('ORD_ID', 'desc')->get();
            }

            return view('ordenes.index', compact('ordenes'));

        } catch (QueryException $e) {
            return redirect()->route('home')
                             ->with('error', 'E1: No hay conexión con la base de datos.');
        }
    }

    public function create()
    {
        // CORRECCIÓN: Traer todos (ya no existen columnas de estado)
        $proveedores = Proveedor::all();
        $productos = Producto::all();
        
        return view('ordenes.create', compact('proveedores', 'productos'));
    }

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
    
    public function show($id)
    {
        $orden = OrdenCompra::with(['proveedor', 'detalles.producto'])->findOrFail($id);
        return view('ordenes.show', compact('orden'));
    }

    public function edit($id)
    {
        $orden = OrdenCompra::with(['detalles'])->findOrFail($id);

        // Validar Estado PENDIENTE
        if ($orden->ORD_ESTADO !== 'PENDIENTE') {
            return redirect()->route('ordenes.index')
                             ->with('error', 'Orden no editable: Ya fue procesada o anulada.');
        }

        // CORRECCIÓN: Traer todos
        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('ordenes.edit', compact('orden', 'proveedores', 'productos'));
    }

    public function update(Request $request, $id)
    {
        try {
            $orden = OrdenCompra::findOrFail($id);

            if ($orden->ORD_ESTADO !== 'PENDIENTE') {
                return redirect()->route('ordenes.index')
                                 ->with('error', 'Orden no editable: Ya fue procesada o anulada.');
            }

            $datos = $request->all();
            OrdenCompra::validar($datos); 
            $orden->actualizarOrdenCompleta($datos);

            return redirect()->route('ordenes.index')
                             ->with('success', 'Orden actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (QueryException $e) {
            return back()->with('error', 'E1/E3: Error de actualización o integridad.');
        }
    }

    public function destroy($id)
    {
        try {
            $orden = OrdenCompra::findOrFail($id);
            
            // Anulación (Borrado Lógico del documento)
            $orden->anular(); 

            return redirect()->route('ordenes.index')
                             ->with('success', 'Orden anulada correctamente.');

        } catch (QueryException $e) {
            return back()->with('error', 'E1: Error al anular el registro.');
        }
    }
}