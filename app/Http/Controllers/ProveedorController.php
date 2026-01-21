<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->input('search');

        if ($criterio) {
            $proveedores = Proveedor::buscarProveedor($criterio);
        } else {
            $proveedores = Proveedor::obtenerProveedores();
        }

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->all();

            Proveedor::validar($datos);
            Proveedor::guardarProveedor($datos);

            return redirect()->route('proveedores.index')
                             ->with('success', 'Proveedor registrado exitosamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function edit($id)
    {
        $proveedor = Proveedor::obtenerProveedor($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        try {
            $proveedor = Proveedor::obtenerProveedor($id);
            $datos = $request->all();

            Proveedor::validar($datos, $id);
            $proveedor->actualizarProveedor($datos);

            return redirect()->route('proveedores.index')
                             ->with('success', 'Proveedor actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function destroy($id)
    {
        $proveedor = Proveedor::obtenerProveedor($id);
        
        // Ejecuta el borrado físico
        $proveedor->eliminarProveedor();

        return redirect()->route('proveedores.index')
                         ->with('success', 'Proveedor eliminado permanentemente.');
    }
}