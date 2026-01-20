<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CarritoDetalle;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    // ==========================================
    // PANTALLA PRINCIPAL (F7.2)
    // ==========================================
    public function index()
    {
        return view('carritos.index');
    }

    // ==========================================
    // F7.2: CONSULTAR TODOS (LISTADO)
    // ==========================================
    public function consultar()
    {
        $carritosActivos = Carrito::obtenerCarritosActivos();
        return view('carritos.index', compact('carritosActivos'));
    }

    // ==========================================
    // F7.3: BUSCAR CARRITO (POR CÉDULA/CORREO)
    // ==========================================
    public function buscarCarrito(Request $request)
    {
        $request->validate(['criterio_carrito' => 'required|string']);

        $carritosEncontrados = Carrito::buscarPorCliente($request->criterio_carrito);

        // CORRECCIÓN: Usamos redirect para asegurar que la sesión flash ('error') se guarde bien
        if ($carritosEncontrados->isEmpty()) {
            return redirect()->route('carritos.index')
                ->with('error', 'No se encontraron carritos para la cédula/correo ingresado.');
        }

        // Si encontramos, retornamos la vista con los datos
        return view('carritos.index', [
            'carritosActivos' => $carritosEncontrados
        ]);
    }

    // ==========================================
    // F7.4: EDITAR CARRITO (CARGA LA VISTA)
    // ==========================================
    public function editar($id)
    {
        $carrito = Carrito::with(['cliente', 'detalles.producto'])->findOrFail($id);
        return view('carritos.edit', compact('carrito'));
    }

    // ==========================================
    // F7.4: FUNCIONALIDAD BUSCAR PRODUCTO
    // (Esta es la que decías que faltaba)
    // ==========================================
    public function buscarProducto(Request $request, $id)
    {
        $request->validate(['criterio' => 'required|string']);
        
        // Cargamos el carrito actual
        $carrito = Carrito::with(['cliente', 'detalles.producto'])->findOrFail($id);
        
        // Buscamos productos
        $productos = Producto::where('PRO_ESTADO', 1)
            ->where(function ($q) use ($request) {
                $q->where('PRO_CODIGO', 'like', "%{$request->criterio}%")
                  ->orWhere('PRO_NOMBRE', 'like', "%{$request->criterio}%");
            })->get();

        // Retornamos la misma vista de edición pero con la variable $productos
        return view('carritos.edit', compact('carrito', 'productos'));
    }

    // ==========================================
    // F7.4: AGREGAR PRODUCTO
    // ==========================================
    public function agregarProducto(Request $request, $id)
    {
        $request->validate([
            'PRO_ID'       => 'required|exists:PRODUCTO,PRO_ID', 
            'DCA_CANTIDAD' => 'required|integer|min:1'
        ]);

        $carrito = Carrito::findOrFail($id);
        $producto = Producto::findOrFail($request->PRO_ID);

        CarritoDetalle::updateOrCreate(
            ['CRD_ID' => $carrito->CRD_ID, 'PRO_ID' => $producto->PRO_ID],
            [
                'DCA_CANTIDAD'        => $request->DCA_CANTIDAD,
                'DCA_PRECIO_UNITARIO' => $producto->PRO_PRECIO,
                'DCA_SUBTOTAL'        => $producto->PRO_PRECIO * $request->DCA_CANTIDAD
            ]
        );

        $this->recalcularTotales($carrito);

        return redirect()->route('carritos.editar', $carrito->CRD_ID);
    }

    // ==========================================
    // F7.4: ACTUALIZAR CANTIDAD (DETALLE)
    // ==========================================
    public function actualizarDetalle(Request $request, $idDetalle)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);

        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $detalle->DCA_CANTIDAD = $request->cantidad;
        $detalle->DCA_SUBTOTAL = $detalle->DCA_PRECIO_UNITARIO * $request->cantidad;
        $detalle->save();

        $carrito = Carrito::findOrFail($detalle->CRD_ID);
        $this->recalcularTotales($carrito);

        return redirect()->route('carritos.editar', $carrito->CRD_ID)
            ->with('success', 'Cantidad actualizada.');
    }

    // ==========================================
    // F7.4: ELIMINAR DETALLE
    // ==========================================
    public function eliminarDetalle($idDetalle)
    {
        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $carritoId = $detalle->CRD_ID;
        
        $detalle->delete();
        
        $carrito = Carrito::findOrFail($carritoId);
        $this->recalcularTotales($carrito);

        return redirect()->route('carritos.editar', $carritoId)
            ->with('success', 'Producto eliminado.');
    }

    // ==========================================
    // F7.5: VACIAR CARRITO
    // ==========================================
    public function vaciar($id)
    {
        $carrito = Carrito::findOrFail($id);
        
        CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)->delete();
        $this->recalcularTotales($carrito);
        
        $carrito->CRD_ESTADO = 'VACIADO';
        $carrito->save();

        // Volvemos al INDEX (Home Carritos)
        return redirect()->route('carritos.index')
            ->with('success', 'El carrito ha sido vaciado correctamente.');
    }

    // ==========================================
    // F7.4: GUARDAR CARRITO (FINALIZAR)
    // ==========================================
    public function guardar($id)
    {
        $carrito = Carrito::with('detalles')->findOrFail($id);
        
        if ($carrito->detalles->isEmpty()) {
            return redirect()->back()->with('error', 'No se puede guardar un carrito vacío');
        }
        
        $carrito->CRD_ESTADO = 'GUARDADO';
        $carrito->save();

        // Volvemos al INDEX (Home Carritos)
        return redirect()->route('carritos.index')
            ->with('success', 'Carrito guardado correctamente.');
    }

    // ==========================================
    // MÉTODOS AUXILIARES (Lógica Interna)
    // ==========================================
    private function recalcularTotales(Carrito $carrito)
    {
        $subtotal = CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)->sum('DCA_SUBTOTAL');
        $carrito->update([
            'CRD_SUBTOTAL' => $subtotal,
            'CRD_IMPUESTO' => $subtotal * 0.12,
            'CRD_TOTAL' => ($subtotal * 1.12)
        ]);
    }

    // Estos métodos (buscarCliente, seleccionarCliente) son para F7.1
    // Los dejo aquí para que el código no se rompa si decides usarlos, 
    // pero no afectan a tu búsqueda de productos.
    public function buscarCliente(Request $request)
    {
        $request->validate(['criterio' => 'required|string']);
        $clientes = Cliente::where('CLI_CEDULA', 'like', "%{$request->criterio}%")
                  ->orWhere('CLI_EMAIL', 'like', "%{$request->criterio}%")->get();

        if ($clientes->isEmpty()) {
            return redirect()->route('carritos.index')->with('error', 'Cliente no encontrado');
        }
        return view('carritos.index', compact('clientes'));
    }

    public function seleccionarCliente($id)
    {
        $cliente = Cliente::findOrFail($id);
        $carrito = Carrito::where('CLI_ID', $cliente->CLI_ID)
            ->whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO']) 
            ->orderBy('CRD_ID', 'DESC')
            ->first();

        if (!$carrito) {
            $carrito = Carrito::create([
                'CLI_ID' => $cliente->CLI_ID, 'CRD_ESTADO' => 'ACTIVO',
                'CRD_SUBTOTAL' => 0, 'CRD_IMPUESTO' => 0, 'CRD_TOTAL' => 0
            ]);
        }
        return redirect()->route('carritos.editar', $carrito->CRD_ID);
    }
}