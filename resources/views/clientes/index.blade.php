@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-uppercase">Gestión de Clientes</h1>
    <a href="{{ route('clientes.create') }}" class="btn btn-dark text-uppercase small">
        + Crear Cliente
    </a>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('clientes.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Buscar por Cédula, Apellido o Email..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-dark w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cli)
                <tr>
                    <td>{{ $cli->CLI_CEDULA }}</td>
                    <td>{{ $cli->CLI_APELLIDOS }} {{ $cli->CLI_NOMBRES }}</td>
                    <td>{{ $cli->CLI_EMAIL }}</td>
                    <td>{{ $cli->CLI_TELEFONO }}</td>
                    <td>
                        @if($cli->CLI_ESTADO == 1)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('clientes.edit', $cli->CLI_ID) }}" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        
                        <form action="{{ route('clientes.destroy', $cli->CLI_ID) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('¿Está seguro de eliminar (desactivar) este cliente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No se encontraron clientes.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection