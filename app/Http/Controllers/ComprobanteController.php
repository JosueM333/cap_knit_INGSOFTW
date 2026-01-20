<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Carrito; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            if ($comprobantes->isEmpty()) {
                session()->flash('info', 'No se encontraron comprobantes emitidos');
            }
            return view('comprobantes.index', compact('comprobantes'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error de conexión: ' . $e->getMessage()]);
        }
    }

    // --- F5.3 Buscar ---
    public function buscar(Request $request)
    {
        try {
            $request->validate(['criterio' => 'required|string']);
            $criterio = $request->input('criterio');
            $comprobantes = Comprobante::buscarPorCriterio($criterio);

            if ($comprobantes->isEmpty()) {
                return redirect()->route('comprobantes.index')
                                 ->withErrors(['search_error' => 'Comprobante no localizado']);
            }
            return view('comprobantes.index', compact('comprobantes'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error de sistema: ' . $e->getMessage()]);
        }
    }

    // --- F5.1 Crear (Vistas y Store) ---
    public function create()
    {
        $ventasPendientes = Carrito::where('CRD_ESTADO', 'GUARDADO')
                                   ->with('cliente')->get();
        return view('comprobantes.create', compact('ventasPendientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'CRD_ID' => 'required|exists:CARRITO,CRD_ID',
            'observaciones' => 'nullable|string|max:255',
        ]);

        if (Comprobante::where('CRD_ID', $request->CRD_ID)->exists()) {
            return back()->withErrors(['error' => 'Esta venta ya ha sido facturada previamente.']);
        }

        DB::beginTransaction();
        try {
            $carrito = Carrito::with(['detalles', 'cliente'])->findOrFail($request->CRD_ID);
            $subtotal = 0;
            foreach ($carrito->detalles as $detalle) {
                $subtotal += ($detalle->DCA_CANTIDAD * $detalle->DCA_PRECIO_UNITARIO);
            }
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            if ($total <= 0) throw new Exception("Total cero.");

            $comprobante = new Comprobante();
            $comprobante->CRD_ID = $carrito->CRD_ID;
            $comprobante->CLI_ID = $carrito->CLI_ID;
            $comprobante->COM_FECHA = now();
            $comprobante->COM_SUBTOTAL = $subtotal;
            $comprobante->COM_IVA = $iva;
            $comprobante->COM_TOTAL = $total;
            $comprobante->COM_OBSERVACIONES = $request->observaciones;
            $comprobante->COM_ESTADO = 'EMITIDO';
            $comprobante->save();

            $carrito->CRD_ESTADO = 'FACTURADA';
            $carrito->save();

            DB::commit();
            return redirect()->route('comprobantes.show', $comprobante->COM_ID)
                             ->with('success', 'Factura emitida correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // --- F5.1 Ver ---
    public function show($id)
    {
        $comprobante = Comprobante::with(['cliente', 'carrito.detalles.producto'])->findOrFail($id);
        return view('comprobantes.show', compact('comprobante'));
    }

    // =========================================================
    // CASO F5.4 – Modificar Comprobante
    // =========================================================

    /**
     * Paso 5: El sistema carga los datos.
     */
    public function edit($id)
    {
        try {
            // E2: El registro solicitado no existe (findOrFail lanza excepción si no lo halla)
            $comprobante = Comprobante::findOrFail($id);
            
            // Validamos que no esté anulado (Lógica de negocio extra)
            if ($comprobante->COM_ESTADO === 'ANULADO') {
                return back()->withErrors(['error' => 'No se puede editar un comprobante ANULADO.']);
            }

            return view('comprobantes.edit', compact('comprobante'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar el comprobante: ' . $e->getMessage()]);
        }
    }

    /**
     * Paso 9 y 10: Validar consistencia y Guardar cambios.
     */
    public function update(Request $request, $id)
    {
        try {
            $comprobante = Comprobante::findOrFail($id);

            // F5.4 Paso 6 (Flujo Alterno): El sistema NO permite modificar campos fiscales.
            // Solo validamos y actualizamos 'observaciones'.
            $request->validate([
                'observaciones' => 'nullable|string|max:255',
            ]);

            // Paso 10: Guardar cambios en la base de datos (E1)
            $comprobante->COM_OBSERVACIONES = $request->input('observaciones');
            $comprobante->save();

            return redirect()->route('comprobantes.index')
                             ->with('success', 'Comprobante actualizado correctamente (Campos no fiscales).');

        } catch (Exception $e) {
            // E1 / E3
            return back()->withErrors(['error' => 'Error de actualización: ' . $e->getMessage()]);
        }
    }

    // =========================================================
    // CASO F5.5 – Borrar (Anular) Comprobante
    // =========================================================

    /**
     * Paso 8 y 9: Borra el registro (lógico) y cambia estado a ANULADO.
     */
    public function anular(Request $request, $id)
    {
        try {
            // E2: Validar que existe
            $comprobante = Comprobante::findOrFail($id);

            // Validar Paso 6: El sistema solicita el motivo.
            $request->validate([
                'motivo_anulacion' => 'required|string|min:5|max:200'
            ]);

            DB::beginTransaction();

            // Paso 9: Cambia el estado a "ANULADO"
            $comprobante->COM_ESTADO = 'ANULADO';
            
            // Guardamos el motivo concatenado en observaciones (para no alterar tu tabla actual)
            $motivo = $request->input('motivo_anulacion');
            $comprobante->COM_OBSERVACIONES .= " | [ANULADO]: " . $motivo;
            
            $comprobante->save(); // E1

            // Opcional: Liberar el Carrito original si se anula la factura
            // $comprobante->carrito->update(['CRD_ESTADO' => 'ANULADO']);

            DB::commit();

            return redirect()->route('comprobantes.index')
                             ->with('success', 'Comprobante ANULADO correctamente.');

        } catch (Exception $e) {
            DB::rollBack(); // E1
            return back()->withErrors(['error' => 'Error al anular comprobante: ' . $e->getMessage()]);
        }
    }
}