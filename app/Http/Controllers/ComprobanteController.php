<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Carrito; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ComprobanteController extends Controller
{
    /**
     * Paso 4: El sistema despliega un listado de Ventas Pendientes
     * (Carritos con estado GUARDADO que aun no son facturados)
     */
    public function create()
    {
        $ventasPendientes = Carrito::where('CRD_ESTADO', 'GUARDADO')
                                   ->with('cliente') // Para mostrar nombre en la vista
                                   ->get();

        return view('comprobantes.create', compact('ventasPendientes'));
    }

    /**
     * Pasos 5 al 13: Lógica de Facturación
     */
    public function store(Request $request)
    {
        // --- Paso 11: Validación de datos ---
        $request->validate([
            'CRD_ID' => 'required|exists:CARRITO,CRD_ID',
            'observaciones' => 'nullable|string|max:255',
        ]);

        // --- Paso 10: Validación de que no haya sido facturada previamente ---
        $existe = Comprobante::where('CRD_ID', $request->CRD_ID)->exists();
        if ($existe) {
            return back()->withErrors(['error' => 'Esta venta ya ha sido facturada previamente.']);
        }

        // Inicio de Transacción (Manejo de Excepciones E1 y E3)
        DB::beginTransaction();

        try {
            // --- Paso 6: Cargar datos de la venta (Carrito, Detalles y Cliente) ---
            $carrito = Carrito::with(['detalles', 'cliente'])->findOrFail($request->CRD_ID);

            // --- Paso 6: Calcular automáticamente IVA (15%) y Total ---
            $subtotal = 0;

            // Recorremos los detalles del carrito para sumar
            foreach ($carrito->detalles as $detalle) {
                // Usamos las columnas de tu modelo CarritoDetalle
                $subtotal += ($detalle->DCA_CANTIDAD * $detalle->DCA_PRECIO_UNITARIO);
            }

            // Cálculo estricto del 15% según Caso de Uso
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            // --- Flujo Alterno: Error en datos de transacción ---
            if ($total <= 0) {
                throw new Exception("Error en datos de transacción: El total no puede ser cero.");
            }

            // --- Paso 7 y 12: Asignar fecha e insertar registro en BD ---
            $comprobante = new Comprobante();
            $comprobante->CRD_ID = $carrito->CRD_ID;
            $comprobante->CLI_ID = $carrito->CLI_ID;
            $comprobante->COM_FECHA = now(); // Paso 7
            $comprobante->COM_SUBTOTAL = $subtotal;
            $comprobante->COM_IVA = $iva;
            $comprobante->COM_TOTAL = $total;
            $comprobante->COM_OBSERVACIONES = $request->observaciones; // Paso 8
            $comprobante->COM_ESTADO = 'EMITIDO';
            
            $comprobante->save(); // Inserta (Paso 12)

            // --- Paso 13: Actualizar estado de la venta a "Facturada" ---
            $carrito->CRD_ESTADO = 'FACTURADA';
            $carrito->save();

            // Confirmar transacción (Éxito)
            DB::commit();

            // --- Paso 13: Generar documento (Redireccionar a la vista del comprobante) ---
            return redirect()->route('comprobantes.show', $comprobante->COM_ID)
                             ->with('success', 'Factura emitida correctamente.');

        } catch (Exception $e) {
            // Manejo de Excepciones E1 y E3
            DB::rollBack();

            // Retorno al paso 5 (formulario) con mensaje de error
            return back()->withErrors(['error' => 'Error al procesar la factura: ' . $e->getMessage()])
                         ->withInput();
        }
    }

    /**
     * Muestra el documento generado
     */
    public function show($id)
    {
        $comprobante = Comprobante::with(['cliente', 'carrito.detalles.producto'])
                                  ->findOrFail($id);
                                  
        return view('comprobantes.show', compact('comprobante'));
    }
}