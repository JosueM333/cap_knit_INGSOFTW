<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CarritoDetalle;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    // Carga inicial de carritos con estado Activo o Guardado
    public function index()
    {
        $carritosActivos = Carrito::obtenerCarritosActivos();
        return view('carritos.index', compact('carritosActivos'));
    }

    // Búsqueda de carritos existentes por datos del cliente
    public function buscarCarrito(Request $request)
    {
        $request->validate(['criterio_carrito' => 'required|string']);
        $carritosEncontrados = Carrito::buscarPorCliente($request->criterio_carrito);

        if ($carritosEncontrados->isEmpty()) {
            return redirect()->route('carritos.index')
                ->with('error', 'No se encontraron carritos para la cédula/correo ingresado.');
        }

        return view('carritos.index', ['carritosActivos' => $carritosEncontrados]);
    }

    public function editar($id)
    {
        $carrito = Carrito::with(['cliente', 'detalles.producto'])->findOrFail($id);
        return view('carritos.edit', compact('carrito'));
    }

    // Busca productos para añadir al carrito en edición
    public function buscarProducto(Request $request, $id)
    {
        $request->validate(['criterio' => 'required|string']);
        $carrito = Carrito::with(['cliente', 'detalles.producto'])->findOrFail($id);

        $productos = Producto::where('PRO_CODIGO', 'like', "%{$request->criterio}%")
            ->orWhere('PRO_NOMBRE', 'like', "%{$request->criterio}%")
            ->get();

        return view('carritos.edit', compact('carrito', 'productos'));
    }

    // Agrega o actualiza un producto en el carrito y recalcula totales
    public function agregarProducto(Request $request, $id)
    {
        $request->validate([
            'PRO_ID' => 'required|exists:PRODUCTO,PRO_ID',
            'DCA_CANTIDAD' => 'required|integer|min:1'
        ]);

        $carrito = Carrito::findOrFail($id);
        $producto = Producto::findOrFail($request->PRO_ID);

        CarritoDetalle::updateOrCreate(
            ['CRD_ID' => $carrito->CRD_ID, 'PRO_ID' => $producto->PRO_ID],
            [
                'DCA_CANTIDAD' => $request->DCA_CANTIDAD,
                'DCA_PRECIO_UNITARIO' => $producto->PRO_PRECIO,
                'DCA_SUBTOTAL' => $producto->PRO_PRECIO * $request->DCA_CANTIDAD
            ]
        );

        $this->recalcularTotales($carrito);
        return redirect()->route('carritos.editar', $carrito->CRD_ID)->with('success', 'Producto agregado.');
    }

    public function actualizarDetalle(Request $request, $idDetalle)
    {
        $request->validate(['cantidad' => 'required|integer|min:1']);

        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $detalle->DCA_CANTIDAD = $request->cantidad;
        $detalle->DCA_SUBTOTAL = $detalle->DCA_PRECIO_UNITARIO * $request->cantidad;
        $detalle->save();

        $this->recalcularTotales(Carrito::findOrFail($detalle->CRD_ID));
        return redirect()->route('carritos.editar', $detalle->CRD_ID)->with('success', 'Cantidad actualizada.');
    }

    public function eliminarDetalle($idDetalle)
    {
        $detalle = CarritoDetalle::findOrFail($idDetalle);
        $id = $detalle->CRD_ID;
        $detalle->delete();

        $this->recalcularTotales(Carrito::findOrFail($id));
        return redirect()->route('carritos.editar', $id)->with('success', 'Producto eliminado.');
    }

    // Vacía el carrito y cambia su estado
    public function vaciar($id)
    {
        $carrito = Carrito::findOrFail($id);
        CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)->delete();

        $this->recalcularTotales($carrito);
        $carrito->update(['CRD_ESTADO' => 'VACIADO']);

        return redirect()->route('carritos.index')->with('success', 'Carrito vaciado.');
    }

    public function guardar($id)
    {
        $carrito = Carrito::findOrFail($id);
        if ($carrito->detalles->isEmpty()) {
            return redirect()->back()->with('error', 'No se puede guardar un carrito vacío');
        }

        $carrito->update(['CRD_ESTADO' => 'GUARDADO']);
        return redirect()->route('carritos.index')->with('success', 'Carrito guardado.');
    }

    // Lógica interna para actualizar subtotal, IVA y total
    private function recalcularTotales(Carrito $carrito)
    {
        $subtotal = CarritoDetalle::where('CRD_ID', $carrito->CRD_ID)->sum('DCA_SUBTOTAL');
        $iva = $subtotal * config('shop.iva');

        $carrito->update([
            'CRD_SUBTOTAL' => $subtotal,
            'CRD_IMPUESTO' => $iva,
            'CRD_TOTAL' => ($subtotal + $iva)
        ]);
    }

    // Búsqueda de cliente para iniciar un nuevo carrito manual
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

    // Selecciona cliente y crea carrito si no tiene uno activo
    public function seleccionarCliente($id)
    {
        $cliente = Cliente::findOrFail($id);
        $carrito = Carrito::where('CLI_ID', $cliente->CLI_ID)
            ->whereIn('CRD_ESTADO', ['ACTIVO', 'GUARDADO'])
            ->first();

        if (!$carrito) {
            $carrito = Carrito::create([
                'CLI_ID' => $cliente->CLI_ID,
                'CRD_FECHA_CREACION' => now(),
                'CRD_ESTADO' => 'ACTIVO',
                'CRD_SUBTOTAL' => 0,
                'CRD_IMPUESTO' => 0,
                'CRD_TOTAL' => 0
            ]);
        }
        return redirect()->route('carritos.editar', $carrito->CRD_ID);
    }
}