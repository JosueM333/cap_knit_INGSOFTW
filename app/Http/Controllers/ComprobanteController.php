<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Carrito;
use App\Models\Bodega;
use App\Models\Kardex;
use App\Models\Transaccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ComprobanteController extends Controller
{
    // --- F5.2 Consultar (Index) ---
    public function index()
    {
        try {
            $comprobantes = Comprobante::with('cliente')
                ->orderBy('COM_ID', 'desc')
                ->get();

            return view('comprobantes.index', compact('comprobantes'));

        } catch (Exception $e) {
            Log::error("Error en index comprobantes: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al cargar los comprobantes.');
        }
    }

    // --- F5.3 Buscar ---
    public function buscar(Request $request)
    {
        try {
            $request->validate(['criterio' => 'required|string']);

            $comprobantes = Comprobante::buscarPorCriterio($request->criterio);

            if ($comprobantes->isEmpty()) {
                session()->flash('error', 'Comprobante no localizado.');
            }

            return view('comprobantes.index', compact('comprobantes'));

        } catch (Exception $e) {
            Log::error("Error buscando comprobante: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al procesar la búsqueda.');
        }
    }

    // --- F5.1 Crear (Vistas y Store) ---
    public function create()
    {
        // Solo mostramos carritos que estén GUARDADOS (listos para facturar)
        $ventasPendientes = Carrito::where('CRD_ESTADO', 'GUARDADO')
            ->with('cliente')->get();

        $bodegas = Bodega::all(); // Nueva: Selección de bodega origen

        return view('comprobantes.create', compact('ventasPendientes', 'bodegas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'CRD_ID' => 'required|exists:CARRITO,CRD_ID',
            'BOD_ID' => 'required|exists:BODEGA,BOD_ID', // Nueva validación
            'observaciones' => 'nullable|string|max:255',
        ]);



        // Validación de negocio: No facturar dos veces
        if (Comprobante::where('CRD_ID', $request->CRD_ID)->exists()) {
            return back()->with('error', 'Esta venta ya ha sido facturada previamente.');
        }

        DB::beginTransaction();
        try {
            $carrito = Carrito::with(['detalles', 'cliente'])->findOrFail($request->CRD_ID);
            $bodega = Bodega::findOrFail($request->BOD_ID);
            $transaccionSalida = Transaccion::where('TRA_CODIGO', 'SALIDA')->firstOrFail();

            // Recálculo de seguridad
            $subtotal = 0;
            foreach ($carrito->detalles as $detalle) {
                // Cálculo de subtotal con precio del detalle (snapshot)
                $subtotal += ($detalle->DCA_CANTIDAD * $detalle->DCA_PRECIO_UNITARIO);

                // VALIDACIÓN DE STOCK
                $pivot = $bodega->productos()->where('PRODUCTO.PRO_ID', $detalle->PRO_ID)->first();
                $stockActual = $pivot ? $pivot->pivot->BP_STOCK : 0;

                if ($stockActual < $detalle->DCA_CANTIDAD) {
                    throw new Exception("Stock insuficiente para el producto ID {$detalle->PRO_ID} en la bodega seleccionada.");
                }

                // DESCUENTO DE STOCK
                $nuevoStock = $stockActual - $detalle->DCA_CANTIDAD;
                $bodega->productos()->updateExistingPivot($detalle->PRO_ID, ['BP_STOCK' => $nuevoStock]);
            }

            // IVA 15%
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            if ($total <= 0)
                throw new Exception("El total a facturar no puede ser cero.");

            // Crear Comprobante
            $comprobante = new Comprobante();
            $comprobante->CRD_ID = $carrito->CRD_ID;
            $comprobante->CLI_ID = $carrito->CLI_ID;
            $comprobante->BOD_ID = $request->BOD_ID; // Guardar Bodega Origen
            $comprobante->COM_FECHA = now();
            $comprobante->COM_SUBTOTAL = $subtotal;
            $comprobante->COM_IVA = $iva;
            $comprobante->COM_TOTAL = $total;
            $comprobante->COM_OBSERVACIONES = $request->observaciones;
            $comprobante->COM_ESTADO = 'EMITIDO';
            $comprobante->save();

            // INSERTAR KARDEX (SALIDA)
            foreach ($carrito->detalles as $detalle) {
                Kardex::create([
                    'BOD_ID' => $request->BOD_ID,
                    'PRO_ID' => $detalle->PRO_ID, // Usamos ID real para Kardex
                    'TRA_ID' => $transaccionSalida->TRA_ID,
                    'ORD_ID' => null,
                    'COM_ID' => $comprobante->COM_ID,
                    'KRD_FECHA' => now(),
                    'KRD_CANTIDAD' => -1 * abs($detalle->DCA_CANTIDAD), // Negativo para salida
                    'KRD_USUARIO' => auth()->user()->name ?? 'Sistema',
                    'KRD_OBSERVACION' => 'Venta Factura #' . $comprobante->COM_ID
                ]);
            }

            // Actualizar estado del carrito
            $carrito->CRD_ESTADO = 'FACTURADA';
            $carrito->save();

            DB::commit();
            return redirect()->route('comprobantes.show', $comprobante->COM_ID)
                ->with('success', 'Factura emitida correctamente y stock actualizado.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error emitiendo factura: " . $e->getMessage());
            return back()->with('error', 'Error al emitir factura: ' . $e->getMessage())->withInput();
        }
    }

    // --- F5.1 Ver Detalle ---
    public function show($id)
    {
        $comprobante = Comprobante::with(['cliente', 'carrito.detalles.producto'])->findOrFail($id);
        return view('comprobantes.show', compact('comprobante'));
    }

    // --- F5.4 Modificar (Solo observaciones) ---
    public function edit($id)
    {
        try {
            $comprobante = Comprobante::findOrFail($id);

            if ($comprobante->COM_ESTADO === 'ANULADO') {
                return redirect()->route('comprobantes.index')
                    ->with('error', 'No se puede editar un comprobante ANULADO.');
            }

            return view('comprobantes.edit', compact('comprobante'));
        } catch (Exception $e) {
            Log::error("Error cargando comprobante para editar ($id): " . $e->getMessage());
            return back()->with('error', 'No se pudo cargar el comprobante solicitado.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $comprobante = Comprobante::findOrFail($id);

            // Solo permitimos editar observaciones (Datos fiscales son inmutables)
            $request->validate([
                'observaciones' => 'nullable|string|max:255',
            ]);

            $comprobante->COM_OBSERVACIONES = $request->input('observaciones');
            $comprobante->save();

            return redirect()->route('comprobantes.index')
                ->with('success', 'Observaciones actualizadas correctamente.');

        } catch (Exception $e) {
            Log::error("Error actualizando comprobante ($id): " . $e->getMessage());
            return back()->with('error', 'Error al actualizar las observaciones.');
        }
    }

    // --- F5.5 Anular (Borrado Lógico) ---
    public function anular(Request $request, $id)
    {
        try {
            $comprobante = Comprobante::with('carrito.detalles')->findOrFail($id);

            $request->validate([
                'motivo_anulacion' => 'required|string|min:5|max:200'
            ]);

            DB::beginTransaction();

            if ($comprobante->COM_ESTADO === 'ANULADO') {
                return back()->with('error', 'El comprobante ya está anulado.');
            }

            // Cambio de Estado
            $comprobante->COM_ESTADO = 'ANULADO';

            // Registrar motivo en historial (append)
            $motivo = $request->input('motivo_anulacion');
            $comprobante->COM_OBSERVACIONES .= " | [ANULADO " . now()->format('d/m/Y') . "]: " . $motivo;
            $comprobante->save();

            // REVERSO DE INVENTARIO (AJUSTE)
            $transaccionAjuste = Transaccion::where('TRA_CODIGO', 'AJUSTE')->first();

            if ($comprobante->BOD_ID) {
                $bodega = Bodega::findOrFail($comprobante->BOD_ID);

                foreach ($comprobante->carrito->detalles as $detalle) {
                    // 1. Devolver Stock
                    $pivot = $bodega->productos()->where('PRODUCTO.PRO_ID', $detalle->PRO_ID)->first();
                    if ($pivot) {
                        $nuevoStock = $pivot->pivot->BP_STOCK + $detalle->DCA_CANTIDAD;
                        $bodega->productos()->updateExistingPivot($detalle->PRO_ID, ['BP_STOCK' => $nuevoStock]);
                    }

                    // 2. Insertar Kardex Ajuste
                    Kardex::create([
                        'BOD_ID' => $comprobante->BOD_ID,
                        'PRO_ID' => $detalle->PRO_ID,
                        'TRA_ID' => $transaccionAjuste->TRA_ID ?? 1,
                        'ORD_ID' => null,
                        'COM_ID' => $comprobante->COM_ID,
                        'KRD_FECHA' => now(),
                        'KRD_CANTIDAD' => $detalle->DCA_CANTIDAD, // Positivo (devolución)
                        'KRD_USUARIO' => auth()->user()->name ?? 'Sistema',
                        'KRD_OBSERVACION' => 'Anulación Factura #' . $comprobante->COM_ID
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('comprobantes.index')
                ->with('success', 'Comprobante ANULADO correctamente y stock restaurado.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error anulando comprobante ($id): " . $e->getMessage());
            return back()->with('error', 'Error inesperado al anular el comprobante: ' . $e->getMessage());
        }
    }
}