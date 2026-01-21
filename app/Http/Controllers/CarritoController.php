<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CarritoDetalle;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function index()
    {
        return view('carritos.index');
    }

    public function consultar()
    {
        $carritosActivos = Carrito::obtenerCarritosActivos();
        return view('carritos.index', compact('carritosActivos'));
    }

    public function buscarCarrito(Request $request)
    {
        $request->validate(['criterio_carrito' => 'required|string']);

        $carritosEncontrados = Carrito::buscarPorCliente($request->criterio_carrito);

        if ($carritosEncontrados->isEmpty()) {
            return redirect()->route('carritos.index')
                ->with('error', 'No se encontraron carritos para la cédula/correo ingresado.');
        }

        return view('carritos.index', [
            'carritosActivos' => $carritosEncontrados
        ]);
    }

    public function editar($id)
    {
        $carrito = Carrito::with(['cliente', 'detalles.producto'])->findOrFail($id);
        return view('carritos.edit', compact('carrito'));
    }

    public function buscarProducto(Request $request, $id)
    {
        $request->validate(['criterio' => 'required|string']);
        
        $carrito = Carrito::with(['cliente', 'detalles.producto'])->findOrFail($id);
        
        // CORRECCIÓN: Se eliminó where('PRO_ESTADO', 1) ya que esa columna fue eliminada.
        // Ahora busca en todos los productos existentes físicamente.
        $productos = Producto::where(function ($q) use ($request) {
                $q->where('PRO_CODIGO', 'like', "%{$request->criterio}%")
                  ->orWhere('PRO_NOMBRE', 'like', "%{$request->criterio}%");
            })->get();

        return view('carritos.edit', compact('carrito', 'productos'));
    }

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

        // Usamos el método del modelo para recalcular
        $carrito->recalcularTotales();

        return redirect()->route('carritos.editar', $carrito->CRD_ID);
    }

    public function actualizarDetalle(Request $request, $idDetalle)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);

        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $detalle->DCA_CANTIDAD = $request->cantidad;
        $detalle->DCA_SUBTOTAL = $detalle->DCA_PRECIO_UNITARIO * $request->cantidad;
        $detalle->save();

        $carrito = Carrito::findOrFail($detalle->CRD_ID);
        $carrito->recalcularTotales();

        return redirect()->route('carritos.editar', $carrito->CRD_ID)
            ->with('success', 'Cantidad actualizada.');
    }

    public function eliminarDetalle($idDetalle)
    {
        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $carritoId = $detalle->CRD_ID;
        
        // Borrado Físico del detalle
        $detalle->delete();
        
        $carrito = Carrito::findOrFail($carritoId);
        $carrito->recalcularTotales();

        return redirect()->route('carritos.editar', $carritoId)
            ->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar($id)
    {
        $carrito = Carrito::findOrFail($id);
        
        // Lógica delegada al modelo
        $carrito->vaciar();

        return redirect()->route('carritos.index')
            ->with('success', 'El carrito ha sido vaciado correctamente.');
    }

    public function guardar($id)
    {
        $carrito = Carrito::with('detalles')->findOrFail($id);
        
        if ($carrito->detalles->isEmpty()) {
            return redirect()->back()->with('error', 'No se puede guardar un carrito vacío');
        }
        
        $carrito->CRD_ESTADO = 'GUARDADO';
        $carrito->save();

        return redirect()->route('carritos.index')
            ->with('success', 'Carrito guardado correctamente.');
    }

    // Métodos para crear carrito desde Cliente (F7.1)
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
        
        // Busca si ya tiene un carrito activo
        $carrito = Carrito::where('CLI_ID', $cliente->CLI_ID)
            ->whereIn('CRD_ESTADO', ['ACTIVO']) 
            ->orderBy('CRD_ID', 'DESC')
            ->first();

        // Si no, crea uno nuevo
        if (!$carrito) {
            $carrito = Carrito::crearCarrito(['CLI_ID' => $cliente->CLI_ID]);
        }
        
        return redirect()->route('carritos.editar', $carrito->CRD_ID);
    }
}