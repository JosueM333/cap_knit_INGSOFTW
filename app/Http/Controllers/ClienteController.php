<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $criterio = $request->input('search');

        if ($criterio) {
            $clientes = Cliente::buscarCliente($criterio);
        } else {
            $clientes = Cliente::obtenerClientes();
        }

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->all();

            Cliente::validar($datos);
            
            Cliente::guardarCliente($datos);

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente registrado exitosamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $datos = $request->all();

            Cliente::validar($datos, $id);
            
            $cliente->actualizarCliente($datos);

            return redirect()->route('clientes.index')
                             ->with('success', 'Cliente actualizado correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        
        // Esto cambiará el estado a 'INACTIVO'
        $cliente->desactivarCliente();

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente eliminado (desactivado) correctamente.');
    }
}