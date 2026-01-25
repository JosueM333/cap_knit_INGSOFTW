<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\DetalleComprobante;
use App\Models\Carrito;
use App\Models\CarritoDetalle; // Importado
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

    // --- F5.1 Crear ---
    public function create()
    {
        $ventasPendientes = Carrito::where('CRD_ESTADO', 'GUARDADO')
            ->with('cliente')
            ->get();

        $bodegas = Bodega::all();

        return view('comprobantes.create', compact('ventasPendientes', 'bodegas'));
    }

    // --- F5.1 Store (Emitir factura) ---
    public function store(Request $request)
    {
        $request->validate([
            'CRD_ID' => 'required|exists:CARRITO,CRD_ID',
            'BOD_ID' => 'required|exists:BODEGA,BOD_ID',
            'observaciones' => 'nullable|string|max:255',
        ]);

        // No facturar dos veces
        if (Comprobante::where('CRD_ID', $request->CRD_ID)->exists()) {
            return back()->with('error', 'Esta venta ya ha sido facturada previamente.');
        }

        DB::beginTransaction();
        try {
            $carrito = Carrito::with(['detalles', 'cliente'])->findOrFail($request->CRD_ID);
            $bodega = Bodega::findOrFail($request->BOD_ID);

            $transaccionSalida = Transaccion::where('TRA_CODIGO', 'SALIDA')->firstOrFail();

            // 1) Validar y descontar stock + calcular subtotal
            $subtotal = 0;

            foreach ($carrito->detalles as $detalle) {

                $cantidad = (int) $detalle->DCA_CANTIDAD;
                $subtotal += $cantidad * (float) $detalle->DCA_PRECIO_UNITARIO;

                // Leer stock desde BODEGA_PRODUCTO (BD1) con lock
                $bp = DB::table('BODEGA_PRODUCTO')
                    ->selectRaw('BP_STOCK as "bp_stock", BP_STOCK_MIN as "bp_stock_min"')
                    ->where('BOD_ID', $bodega->BOD_ID)
                    ->where('PRO_ID', $detalle->PRO_ID)
                    ->lockForUpdate()
                    ->first();

                $stockActual = $bp ? (int) $bp->bp_stock : 0;

                if ($stockActual < $cantidad) {
                    throw new Exception(
                        "Stock insuficiente para el producto ID {$detalle->PRO_ID} en la bodega seleccionada. " .
                        "Stock actual: {$stockActual}, requerido: {$cantidad}"
                    );
                }

                $nuevoStock = $stockActual - $cantidad;

                // Actualizar stock en pivote
                DB::table('BODEGA_PRODUCTO')
                    ->where('BOD_ID', $bodega->BOD_ID)
                    ->where('PRO_ID', $detalle->PRO_ID)
                    ->update([
                        'BP_STOCK' => $nuevoStock,
                        'UPDATED_AT' => now(),
                    ]);
            }

            // 2) IVA y total
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            if ($total <= 0) {
                throw new Exception("El total a facturar no puede ser cero.");
            }

            /**
             * =========================================================
             * FIX DBLINK:
             * Oracle NO soporta RETURNING INTO por DBLINK.
             * Entonces generamos COM_ID manualmente desde la SEQUENCE.
             * =========================================================
             */
            $nextCom = DB::selectOne('SELECT COMPROBANTE_SEQ.NEXTVAL AS ID FROM DUAL');
            $comprobanteId = (int) $nextCom->id;

            // 3) Crear Comprobante (BD2 remoto) con ID explícito
            $comprobante = new Comprobante();
            $comprobante->COM_ID = $comprobanteId;  // ✅ CLAVE
            $comprobante->CRD_ID = $carrito->CRD_ID;
            $comprobante->CLI_ID = $carrito->CLI_ID;

            // Mantienes BOD_ID como dato (sin FK real entre BD)
            $comprobante->BOD_ID = $request->BOD_ID;

            $comprobante->COM_FECHA = now();
            $comprobante->COM_SUBTOTAL = $subtotal;
            $comprobante->COM_IVA = $iva;
            $comprobante->COM_TOTAL = $total;
            $comprobante->COM_OBSERVACIONES = $request->observaciones;
            $comprobante->COM_ESTADO = 'EMITIDO';
            $comprobante->save();

            // 3.5) Crear DetalleComprobante (BD2 remoto) con snapshots
            foreach ($carrito->detalles as $detalle) {

                // Recomendado: generar DCO_ID manual para evitar RETURNING por DBLINK
                $nextDet = DB::selectOne('SELECT DETALLE_COMPROBANTE_SEQ.NEXTVAL AS ID FROM DUAL');
                $detalleId = (int) $nextDet->id;

                $dc = new DetalleComprobante();
                $dc->DCO_ID = $detalleId;         // ✅ CLAVE
                $dc->COM_ID = $comprobanteId;     // ✅ CLAVE
                $dc->PRO_ID = $detalle->PRO_ID;

                $dc->PRO_CODIGO_SNAP = $detalle->PRO_CODIGO;
                $dc->PRO_NOMBRE_SNAP = $detalle->PRO_NOMBRE;

                $dc->DCO_CANTIDAD = (int) $detalle->DCA_CANTIDAD;
                $dc->DCO_PRECIO_UNITARIO = (float) $detalle->DCA_PRECIO_UNITARIO;
                $dc->DCO_SUBTOTAL = (float) $detalle->DCA_SUBTOTAL;

                $dc->save();
            }

            // 4) Kardex (Salida) — BD1
            foreach ($carrito->detalles as $detalle) {
                Kardex::create([
                    'BOD_ID' => $request->BOD_ID,
                    'PRO_ID' => $detalle->PRO_ID,
                    'TRA_ID' => $transaccionSalida->TRA_ID,
                    'ORD_ID' => null,
                    'COM_ID' => $comprobanteId, // ✅ usar el ID manual
                    'KRD_FECHA' => now(),
                    'KRD_CANTIDAD' => -1 * abs((int) $detalle->DCA_CANTIDAD),
                    'KRD_USUARIO' => auth()->user()->name ?? 'Sistema',
                    'KRD_OBSERVACION' => 'Venta Factura #' . $comprobanteId
                ]);
            }

            // 5) Marcar carrito como facturado
            $carrito->CRD_ESTADO = 'FACTURADA';
            $carrito->save();

            // 6) Limpiar DETALLE_CARRITO (Requisito: eliminar detalle físico tras facturar)
            // Se asume que los datos ya viven en DETALLE_COMPROBANTE
            CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)->delete();

            DB::commit();

            return redirect()->route('comprobantes.show', $comprobanteId)
                ->with('success', 'Factura emitida correctamente y stock actualizado.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error emitiendo factura: " . $e->getMessage());

            return back()
                ->with('error', 'Error al emitir factura: ' . $e->getMessage())
                ->withInput();
        }
    }

    // --- F5.1 Ver Detalle ---
    public function show($id)
    {
        $comprobante = Comprobante::with(['cliente', 'detalles'])->findOrFail($id);
        return view('comprobantes.show', compact('comprobante'));
    }

    // --- F5.4 Edit ---
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

    // --- F5.5 Anular ---
    public function anular(Request $request, $id)
    {
        try {
            // OJO: si tu relación carrito/detalles también está distribuida,
            // esto puede fallar. Si te da problemas, migramos a usar DetalleComprobante.
            $comprobante = Comprobante::with('carrito.detalles')->findOrFail($id);

            $request->validate([
                'motivo_anulacion' => 'required|string|min:5|max:200'
            ]);

            DB::beginTransaction();

            if ($comprobante->COM_ESTADO === 'ANULADO') {
                return back()->with('error', 'El comprobante ya está anulado.');
            }

            $comprobante->COM_ESTADO = 'ANULADO';

            $motivo = $request->input('motivo_anulacion');
            $comprobante->COM_OBSERVACIONES = trim(
                ($comprobante->COM_OBSERVACIONES ?? '') . " | [ANULADO " . now()->format('d/m/Y') . "]: " . $motivo
            );
            $comprobante->save();

            $transaccionAjuste = Transaccion::where('TRA_CODIGO', 'AJUSTE')->first();

            if ($comprobante->BOD_ID) {
                $bodegaId = $comprobante->BOD_ID;

                foreach ($comprobante->carrito->detalles as $detalle) {

                    $cantidad = (int) $detalle->DCA_CANTIDAD;

                    $bp = DB::table('BODEGA_PRODUCTO')
                        ->selectRaw('BP_STOCK as "bp_stock"')
                        ->where('BOD_ID', $bodegaId)
                        ->where('PRO_ID', $detalle->PRO_ID)
                        ->lockForUpdate()
                        ->first();

                    if ($bp) {
                        $stockActual = (int) $bp->bp_stock;
                        $nuevoStock = $stockActual + $cantidad;

                        DB::table('BODEGA_PRODUCTO')
                            ->where('BOD_ID', $bodegaId)
                            ->where('PRO_ID', $detalle->PRO_ID)
                            ->update([
                                'BP_STOCK' => $nuevoStock,
                                'UPDATED_AT' => now(),
                            ]);
                    }

                    Kardex::create([
                        'BOD_ID' => $bodegaId,
                        'PRO_ID' => $detalle->PRO_ID,
                        'TRA_ID' => $transaccionAjuste->TRA_ID ?? 1,
                        'ORD_ID' => null,
                        'COM_ID' => $comprobante->COM_ID,
                        'KRD_FECHA' => now(),
                        'KRD_CANTIDAD' => abs($cantidad),
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
