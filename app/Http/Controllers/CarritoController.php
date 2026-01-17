<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CarritoController extends Controller
{
    /* =========================
       F7.2 / F7.3 – Listar / Buscar carritos
       ========================= */

    public function index(Request $request)
    {
        $criterio = $request->input('search');

        if ($criterio) {
            $carritos = Carrito::buscarPorCliente($criterio);
        } else {
            $carritos = Carrito::obtenerCarritosActivos();
        }

        return view('carritos.index', compact('carritos'));
    }


    /* =========================
       F7.1 – Crear carrito
       ========================= */

    public function create()
    {
        return view('carritos.create');
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->all();

            Carrito::validar($datos);
            Carrito::crearCarrito($datos);

            return redirect()
                ->route('carritos.index')
                ->with('success', 'Carrito creado correctamente');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /* =========================
       F7.4 – Editar carrito
       ========================= */

    public function edit($id)
    {
        $carrito = Carrito::with('detalles.producto')->findOrFail($id);

        return view('carritos.edit', compact('carrito'));
    }

    public function update(Request $request, $id)
    {
        try {
            $carrito = Carrito::findOrFail($id);
            $carrito->recalcularTotales();

            return redirect()
                ->route('carritos.index')
                ->with('success', 'Carrito actualizado correctamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar el carrito');
        }
    }

    /* =========================
       F7.5 – Vaciar carrito
       ========================= */

    public function destroy($id)
    {
        $carrito = Carrito::findOrFail($id);
        $carrito->vaciar();

        return redirect()
            ->route('carritos.index')
            ->with('success', 'Carrito vaciado correctamente');
    }
}
