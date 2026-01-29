<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Bodega;
use App\Models\Kardex;
use App\Models\Transaccion;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    public function index(Request $request)
    {
        try {
            $criterio = $request->input('criterio');

            // Pasamos Bodegas para el Modal de Recepción
            $bodegas = Bodega::all(); // Necesario para el modal en index

            if ($criterio) {
                $ordenes = OrdenCompra::buscar($criterio);

                if ($ordenes->isEmpty()) {
                    session()->flash('error', 'Orden de compra no localizada.');
                }
            } else {
                $ordenes = OrdenCompra::with('proveedor')->orderBy('ORD_ID', 'desc')->get();
            }

            return view('ordenes.index', compact('ordenes', 'bodegas'));

        } catch (QueryException $e) {
            return redirect()->route('home')
                ->with('error', 'E1: No hay conexión con la base de datos.');
        }
    }

    public function create()
    {
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

        if ($orden->ORD_ESTADO !== 'PENDIENTE') {
            return redirect()->route('ordenes.index')
                ->with('error', 'Orden no editable: Ya fue procesada o anulada.');
        }

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
            $orden->anular();

            return redirect()->route('ordenes.index')
                ->with('success', 'Orden anulada correctamente.');

        } catch (QueryException $e) {
            return back()->with('error', 'E1: Error al anular el registro.');
        }
    }

    // NUEVO: Método para Recibir Orden de Compra (Entrada de Inventario)
    public function recibirOrden(Request $request, $id)
    {
        $request->validate([
            'BOD_ID' => 'required|exists:BODEGA,BOD_ID'
        ]);

        DB::beginTransaction();
        try {
            $orden = OrdenCompra::with('detalles')->findOrFail($id);

            if ($orden->ORD_ESTADO !== 'PENDIENTE') {
                return back()->with('error', 'La orden ya fue procesada o anulada.');
            }

            // 1. Obtener Transacción tipo ENTRADA
            $transaccion = Transaccion::where('TRA_CODIGO', 'ENTRADA')->firstOrFail();

            // 2. Procesar Detalles
            foreach ($orden->detalles as $detalle) {
                // Actualizar/Crear Stock en BodegaProducto con Lógica de Suma Directa
                $bodegaId = $request->BOD_ID;
                $productoId = $detalle->PRO_ID;
                $cantidadEntrante = $detalle->DOR_CANTIDAD;

                // Verificar si existe registro en la tabla pivote (BODEGA_PRODUCTO)
                // Usamos DB facade para ser más explícitos y evitar problemas de cache de Eloquent
                $stockActualRegistro = DB::table('BODEGA_PRODUCTO')
                    ->where('BOD_ID', $bodegaId)
                    ->where('PRO_ID', $productoId)
                    ->first();

                if ($stockActualRegistro) {
                    // Si existe, SUMAMOS la cantidad entrante al stock actual
                    $nuevoStock = $stockActualRegistro->bp_stock + $cantidadEntrante;

                    DB::table('BODEGA_PRODUCTO')
                        ->where('BOD_ID', $bodegaId)
                        ->where('PRO_ID', $productoId)
                        ->update(['bp_stock' => $nuevoStock, 'updated_at' => now()]);
                } else {
                    // Si no existe, creamos el registro con la cantidad entrante
                    DB::table('BODEGA_PRODUCTO')->insert([
                        'BOD_ID' => $bodegaId,
                        'PRO_ID' => $productoId,
                        'bp_stock' => $cantidadEntrante,
                        'bp_stock_min' => 5, // Default
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // 3. Insertar Kardex
                Kardex::create([
                    'BOD_ID' => $request->BOD_ID,
                    'PRO_ID' => $detalle->PRO_ID,
                    'TRA_ID' => $transaccion->TRA_ID,
                    'ORD_ID' => $orden->ORD_ID,
                    'COM_ID' => null,
                    'KRD_FECHA' => now(),
                    'KRD_CANTIDAD' => $detalle->DOR_CANTIDAD, // Positivo
                    'KRD_SALDO' => null,
                    'KRD_USUARIO' => auth()->user()->name ?? 'Sistema',
                    'KRD_OBSERVACION' => 'Recepción de OC #' . $orden->ORD_ID
                ]);
            }

            // 4. Actualizar Estado Orden
            $orden->update([
                'ORD_ESTADO' => 'RECIBIDA',
                'BOD_ID' => $request->BOD_ID
            ]);

            DB::commit();
            return redirect()->route('ordenes.index')->with('success', 'Mercadería recibida y stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al recibir: ' . $e->getMessage());
        }
    }
}