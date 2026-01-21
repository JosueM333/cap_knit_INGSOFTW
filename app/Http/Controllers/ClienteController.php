<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if ($search) {
            $clientes = Cliente::buscarCliente($search);
        } else {
            $clientes = Cliente::where('CLI_ESTADO', 'ACTIVO')
                             ->orderBy('cli_id', 'DESC') // Ojo: cli_id en minúscula para coincidir con el modelo
                             ->get();
        }
        
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        // 1. Validar
        Cliente::validar($request->all());
        
        // 2. Preparar datos
        $datos = $request->all();
        // Encriptamos la contraseña (que viene en minúsculas desde el form)
        $datos['cli_password'] = bcrypt($request->input('cli_password'));
        $datos['cli_estado'] = 'ACTIVO';

        // 3. Crear
        Cliente::create($datos);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente creado exitosamente.');
    }

    public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        
        // 1. Validar
        Cliente::validar($request->all(), $id);
        
        // 2. SOLUCIÓN AL ERROR ORA-01407
        // Primero obtenemos todos los datos MENOS la contraseña
        $datos = $request->except(['cli_password']); 
        
        // Solo si el usuario escribió una nueva contraseña, la agregamos al array
        if ($request->filled('cli_password')) {
            $datos['cli_password'] = bcrypt($request->input('cli_password'));
        }
        
        // Nota: Al usar except() arriba, si el campo estaba vacío, 
        // la clave 'cli_password' desaparece del array $datos.
        // Eloquent NO tocará esa columna en la BD y se mantendrá la contraseña anterior.

        // 3. Actualizar
        $cliente->update($datos);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        // Borrado lógico usando la clave en minúsculas definida en el modelo
        $cliente->update(['cli_estado' => 'INACTIVO']);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente eliminado (desactivado) correctamente.');
    }
}