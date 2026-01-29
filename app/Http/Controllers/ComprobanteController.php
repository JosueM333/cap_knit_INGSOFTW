<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\DetalleComprobante;
use App\Models\Carrito;
use App\Models\CarritoDetalle; // Importado (si luego decides borrar detalle carrito desde app)
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
            'CRD_ID' => 'required|exists:oracle_guayaquil.CARRITO,CRD_ID',
            // 'BOD_ID' => 'required|exists:oracle.BODEGA,BOD_ID', // Ya no se valida como input
            'observaciones' => 'nullable|string|max:255',
        ]);

        // No facturar dos veces
        if (Comprobante::where('CRD_ID', $request->CRD_ID)->exists()) {
            return back()->with('error', 'Esta venta ya ha sido facturada previamente.');
        }

        // INICIO TRANSACCIÓN DISTRIBUIDA (Best Effort)
        DB::connection('oracle')->beginTransaction();
        DB::connection('oracle_guayaquil')->beginTransaction();

        try {
            // 1. Obtener Carrito (BD2 - Sales)
            // Nota: No usamos 'detalles.producto' si es cross-db. Cargamos solo detalles.
            $carrito = Carrito::with(['detalles', 'cliente'])->findOrFail($request->CRD_ID);

            // Validar bodega (BD1 - Inventory)
            // $bodega = Bodega::findOrFail($request->BOD_ID);

            // BUSCAR BODEGA POR DEFECTO AUTOMÁTICAMENTE
            $bodegaDefecto = Bodega::where('BOD_ES_DEFECTO', 1)->first();
            if (!$bodegaDefecto) {
                throw new Exception("No hay una bodega predeterminada configurada en el sistema. Por favor configure una en Gestión de Bodegas.");
            }
            $bodegaId = $bodegaDefecto->BOD_ID;

            $transaccionSalida = Transaccion::where('TRA_CODIGO', 'SALIDA')->firstOrFail();

            // 2. Calcular Totales (Datos vienen del carrito en BD2)
            $subtotal = 0;
            foreach ($carrito->detalles as $detalle) {
                $cantidad = (int) $detalle->DCA_CANTIDAD;
                $subtotal += $cantidad * (float) $detalle->DCA_PRECIO_UNITARIO;
            }

            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            if ($total <= 0) {
                throw new Exception("El total a facturar no puede ser cero.");
            }

            // 3. Crear Comprobante (BD2 - Sales)
            $comprobante = new Comprobante();
            $comprobante->CRD_ID = $carrito->CRD_ID;
            $comprobante->CLI_ID = $carrito->CLI_ID;
            $comprobante->BOD_ID = $bodegaId; // Asignamos la bodega por defecto
            $comprobante->COM_FECHA = now();
            $comprobante->COM_SUBTOTAL = $subtotal;
            $comprobante->COM_IVA = $iva;
            $comprobante->COM_TOTAL = $total;
            $comprobante->COM_OBSERVACIONES = $request->observaciones;
            $comprobante->COM_ESTADO = 'EMITIDO';
            $comprobante->save(); // Save in oracle_guayaquil

            /**
             * 4. Procesar Detalles:
             *    - Validar Stock (BD1)
             *    - Descontar Stock (BD1)
             *    - Crear Detalle Comprobante (BD2)
             *    - Crear Kardex (BD1)
             */

            foreach ($carrito->detalles as $detalle) {
                $proId = $detalle->PRO_ID;
                $cantidad = (int) $detalle->DCA_CANTIDAD;

                // A) Validar y Bloquear Stock en BD1 (Oracle)
                $bp = DB::connection('oracle')->table('BODEGA_PRODUCTO')
                    ->selectRaw('bp_stock')
                    ->where('BOD_ID', $bodegaId)
                    ->where('PRO_ID', $proId)
                    ->lockForUpdate()
                    ->first();

                if (!$bp) {
                    // Podemos intentar buscar el nombre del producto para el error
                    $prodName = DB::connection('oracle')->table('PRODUCTO')->where('PRO_ID', $proId)->value('PRO_NOMBRE') ?? "ID $proId";
                    throw new Exception("El producto '$prodName' no existe en la bodega seleccionada.");
                }

                $stockActual = (int) $bp->bp_stock; // Nota: Oracle devuelve columnas en mayúsculas a veces, o lower. Usar array access o standard. DB::table suele devolver obj con nombres exactos.

                if ($stockActual < $cantidad) {
                    $prodName = DB::connection('oracle')->table('PRODUCTO')->where('PRO_ID', $proId)->value('PRO_NOMBRE') ?? "ID $proId";
                    throw new Exception("Stock insuficiente para '$prodName' (Disponible: $stockActual).");
                }

                // B) Descontar Stock en BD1
                DB::connection('oracle')->table('BODEGA_PRODUCTO')
                    ->where('BOD_ID', $bodegaId)
                    ->where('PRO_ID', $proId)
                    ->update([
                        'bp_stock' => $stockActual - $cantidad,
                        'updated_at' => now(), // Asegurarse que columna existe en lower o UPPER según migración
                    ]);

                // C) Crear Detalle Comprobante en BD2
                $dc = new DetalleComprobante();
                $dc->COM_ID = $comprobante->COM_ID;
                $dc->PRO_ID = $proId; // ID lógico
                $dc->DCO_CANTIDAD = $cantidad;
                $dc->DCO_PRECIO_UNITARIO = (float) $detalle->DCA_PRECIO_UNITARIO;
                $dc->DCO_SUBTOTAL = (float) $detalle->DCA_SUBTOTAL;
                $dc->save(); // Save in oracle_guayaquil

                // D) Crear Kardex en BD1
                Kardex::create([ // Modelo Kardex usa conexión 'oracle'
                    'BOD_ID' => $bodegaId,
                    'PRO_ID' => $proId,
                    'TRA_ID' => $transaccionSalida->TRA_ID,
                    'ORD_ID' => null,
                    'COM_ID' => $comprobante->COM_ID, // ID de Comprobante (BD2) guardado en Kardex (BD1) como referencia
                    'KRD_FECHA' => now(),
                    'KRD_CANTIDAD' => -1 * abs($cantidad),
                    'KRD_USUARIO' => auth()->user()->name ?? 'Sistema',
                    'KRD_OBSERVACION' => 'Venta Factura #' . $comprobante->COM_ID
                ]);
            }

            // 5) Marcar carrito como facturado (BD2)
            $carrito->CRD_ESTADO = 'FACTURADA';
            $carrito->save();

            // 6) Borrar detalles del carrito (BD2)
            $carrito->detalles()->delete();

            // COMMIT AMBAS
            DB::connection('oracle_guayaquil')->commit();
            DB::connection('oracle')->commit();

            return redirect()->route('comprobantes.show', $comprobante->COM_ID)
                ->with('success', 'Factura emitida correctamente y stock actualizado.');

        } catch (Exception $e) {
            // ROLLBACK AMBAS
            DB::connection('oracle_guayaquil')->rollBack();
            DB::connection('oracle')->rollBack();

            Log::error("Error emitiendo factura: " . $e->getMessage());

            return back()
                ->with('error', 'Error al emitir factura: ' . $e->getMessage())
                ->withInput();
        }
    }

    // --- F5.1 Ver Detalle ---
    public function show($id)
    {
        // Traemos Comprobante (BD2)
        $comprobante = Comprobante::with(['cliente', 'detalles'])->findOrFail($id);

        // Enriquecer visualización: cargar nombres de productos desde BD1
        // Obtenemos IDs de productos
        $proIds = $comprobante->detalles->pluck('PRO_ID')->unique();

        // Buscamos info en BD1
        $productosInfo = DB::connection('oracle')->table('PRODUCTO')
            ->whereIn('PRO_ID', $proIds)
            ->pluck('pro_nombre', 'pro_id'); // Retorna [id => nombre]

        // Inyectamos nombre en objeto detalle (atributo dinámico para la vista)
        foreach ($comprobante->detalles as $detalle) {
            $detalle->producto_nombre = $productosInfo[$detalle->PRO_ID] ?? 'Producto Desconocido';
        }

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
            // Cargar comprobante BD2
            $comprobante = Comprobante::with('detalles')->findOrFail($id);
            // Nota: 'carrito.detalles' ya se borraron en el store. Usamos $comprobante->detalles

            $request->validate([
                'motivo_anulacion' => 'required|string|min:5|max:200'
            ]);

            if ($comprobante->COM_ESTADO === 'ANULADO') {
                return back()->with('error', 'El comprobante ya está anulado.');
            }

            // TRANSACCIÓN DISTRIBUIDA
            DB::connection('oracle')->beginTransaction();
            DB::connection('oracle_guayaquil')->beginTransaction();

            try {
                // 1. Actualizar Comprobante (BD2)
                $comprobante->COM_ESTADO = 'ANULADO';
                $motivo = $request->input('motivo_anulacion');
                $comprobante->COM_OBSERVACIONES = trim(
                    ($comprobante->COM_OBSERVACIONES ?? '') . " | [ANULADO " . now()->format('d/m/Y') . "]: " . $motivo
                );
                $comprobante->save();

                // 2. Restaurar Stock en BD1
                $transaccionAjuste = Transaccion::where('TRA_CODIGO', 'AJUSTE')->first(); // BD1

                if ($comprobante->BOD_ID) {
                    $bodegaId = $comprobante->BOD_ID;

                    foreach ($comprobante->detalles as $detalle) {
                        $cantidad = (int) $detalle->DCO_CANTIDAD; // Usamos cantidad del detalle comprobante
                        $proId = $detalle->PRO_ID;

                        // Lock en BD1
                        $bp = DB::connection('oracle')->table('BODEGA_PRODUCTO')
                            ->selectRaw('bp_stock')
                            ->where('BOD_ID', $bodegaId)
                            ->where('PRO_ID', $proId)
                            ->lockForUpdate()
                            ->first();

                        if ($bp) {
                            $stockActual = (int) $bp->bp_stock;
                            $nuevoStock = $stockActual + $cantidad;

                            DB::connection('oracle')->table('BODEGA_PRODUCTO')
                                ->where('BOD_ID', $bodegaId)
                                ->where('PRO_ID', $proId)
                                ->update([
                                    'bp_stock' => $nuevoStock,
                                    'updated_at' => now(),
                                ]);
                        }

                        // Kardex en BD1
                        Kardex::create([
                            'BOD_ID' => $bodegaId,
                            'PRO_ID' => $proId,
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

                DB::connection('oracle_guayaquil')->commit();
                DB::connection('oracle')->commit();

                return redirect()->route('comprobantes.index')
                    ->with('success', 'Comprobante ANULADO correctamente y stock restaurado.');
            } catch (Exception $e2) {
                DB::connection('oracle_guayaquil')->rollBack();
                DB::connection('oracle')->rollBack();
                throw $e2;
            }

        } catch (Exception $e) {
            Log::error("Error anulando comprobante ($id): " . $e->getMessage());
            return back()->with('error', 'Error inesperado al anular el comprobante: ' . $e->getMessage());
        }
    }
}
