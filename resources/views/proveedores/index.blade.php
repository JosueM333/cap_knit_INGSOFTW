@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-uppercase">Gestión de Proveedores</h1>
    <a href="{{ route('proveedores.create') }}" class="btn btn-dark text-uppercase small">
        + Nuevo Proveedor
    </a>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('proveedores.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Buscar por RUC o Razón Social..." 
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
                    <th>RUC</th>
                    <th>Razón Social</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Contacto</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $prov)
                <tr>
                    <td>{{ $prov->PRV_RUC }}</td>
                    <td class="fw-bold">{{ $prov->PRV_NOMBRE }}</td>
                    <td>{{ $prov->PRV_TELEFONO }}</td>
                    <td>{{ $prov->PRV_EMAIL }}</td>
                    <td>{{ $prov->PRV_PERSONA_CONTACTO ?? '-' }}</td>
                    <td class="text-end">
                        <a href="{{ route('proveedores.edit', $prov->PRV_ID) }}" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        
                        <form action="{{ route('proveedores.destroy', $prov->PRV_ID) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('¿Está seguro de eliminar físicamente al proveedor {{ $prov->PRV_NOMBRE }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No existen proveedores registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection