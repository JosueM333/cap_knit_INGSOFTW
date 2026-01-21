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
            
            return view('comprobantes.index', compact('comprobantes'));

        } catch (Exception $e) {
            return back()->with('error', 'Error de conexión: ' . $e->getMessage());
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
            return back()->with('error', 'Error de sistema: ' . $e->getMessage());
        }
    }

    // --- F5.1 Crear (Vistas y Store) ---
    public function create()
    {
        // Solo mostramos carritos que estén GUARDADOS (listos para facturar)
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

        // Validación de negocio: No facturar dos veces
        if (Comprobante::where('CRD_ID', $request->CRD_ID)->exists()) {
            return back()->with('error', 'Esta venta ya ha sido facturada previamente.');
        }

        DB::beginTransaction();
        try {
            $carrito = Carrito::with(['detalles', 'cliente'])->findOrFail($request->CRD_ID);
            
            // Recálculo de seguridad (aunque ya debería estar en el carrito)
            $subtotal = 0;
            foreach ($carrito->detalles as $detalle) {
                $subtotal += ($detalle->DCA_CANTIDAD * $detalle->DCA_PRECIO_UNITARIO);
            }
            
            // IVA 15% (Según tu lógica actual)
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            if ($total <= 0) throw new Exception("El total a facturar no puede ser cero.");

            // Crear Comprobante
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

            // Actualizar estado del carrito para cerrar el ciclo
            $carrito->CRD_ESTADO = 'FACTURADA';
            $carrito->save();

            DB::commit();
            return redirect()->route('comprobantes.show', $comprobante->COM_ID)
                             ->with('success', 'Factura emitida correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
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
            return back()->with('error', 'Error al cargar el comprobante.');
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
            return back()->with('error', 'Error de actualización: ' . $e->getMessage());
        }
    }

    // --- F5.5 Anular (Borrado Lógico) ---
    public function anular(Request $request, $id)
    {
        try {
            $comprobante = Comprobante::findOrFail($id);

            $request->validate([
                'motivo_anulacion' => 'required|string|min:5|max:200'
            ]);

            DB::beginTransaction();

            // Cambio de Estado
            $comprobante->COM_ESTADO = 'ANULADO';
            
            // Registrar motivo en historial (append)
            $motivo = $request->input('motivo_anulacion');
            $comprobante->COM_OBSERVACIONES .= " | [ANULADO " . now()->format('d/m/Y') . "]: " . $motivo;
            
            $comprobante->save();

            DB::commit();

            return redirect()->route('comprobantes.index')
                             ->with('success', 'Comprobante ANULADO correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }
}