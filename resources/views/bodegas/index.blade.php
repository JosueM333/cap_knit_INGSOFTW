@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-uppercase">Gestión de Bodegas</h1>
    <a href="{{ route('bodegas.create') }}" class="btn btn-dark text-uppercase small">
        + Nueva Bodega
    </a>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('bodegas.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Buscar por Nombre o Ubicación..." 
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
                    <th>Nombre</th>
                    <th>Ubicación</th>
                    <th>Descripción</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bodegas as $bodega)
                <tr>
                    <td class="fw-bold">{{ $bodega->BOD_NOMBRE }}</td>
                    <td>{{ $bodega->BOD_UBICACION }}</td>
                    <td>{{ $bodega->BOD_DESCRIPCION ?? '---' }}</td>
                    <td class="text-end">
                        <a href="{{ route('bodegas.edit', $bodega->BOD_ID) }}" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        
                        <form action="{{ route('bodegas.destroy', $bodega->BOD_ID) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('ATENCIÓN: Se eliminará FÍSICAMENTE esta bodega.\n¿Está seguro?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No existen bodegas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection