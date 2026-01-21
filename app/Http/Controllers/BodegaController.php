<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BodegaController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->input('search');

        if ($criterio) {
            $bodegas = Bodega::buscarBodega($criterio);
        } else {
            $bodegas = Bodega::obtenerBodegas();
        }

        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        return view('bodegas.create');
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->all();

            Bodega::validar($datos);
            Bodega::guardarBodega($datos);

            return redirect()->route('bodegas.index')
                             ->with('success', 'Bodega registrada exitosamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function edit($id)
    {
        $bodega = Bodega::obtenerBodega($id);
        return view('bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, $id)
    {
        try {
            $bodega = Bodega::obtenerBodega($id);
            $datos = $request->all();

            Bodega::validar($datos, $id);
            $bodega->actualizarBodega($datos);

            return redirect()->route('bodegas.index')
                             ->with('success', 'Bodega actualizada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function destroy($id)
    {
        $bodega = Bodega::obtenerBodega($id);
        
        // Ejecuta borrado físico
        $bodega->eliminarBodega();

        return redirect()->route('bodegas.index')
                         ->with('success', 'Bodega eliminada permanentemente.');
    }
}