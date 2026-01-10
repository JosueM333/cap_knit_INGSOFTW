@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-uppercase">Gestión de Productos</h1>
    <a href="{{ route('productos.create') }}" class="btn btn-dark text-uppercase small">
        + Nuevo Producto
    </a>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('productos.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Buscar por Código SKU o Nombre..." 
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
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Proveedor</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $prod)
                <tr>
                    <td class="font-monospace">{{ $prod->PRO_CODIGO }}</td>
                    <td>
                        <div class="fw-bold">{{ $prod->PRO_NOMBRE }}</div>
                        <small class="text-muted">{{ $prod->PRO_MARCA }} - {{ $prod->PRO_COLOR }}</small>
                    </td>
                    <td class="fw-bold text-success">${{ number_format($prod->PRO_PRECIO, 2) }}</td>
                    <td>{{ $prod->proveedor->PRV_NOMBRE }}</td>
                    <td>
                        @if($prod->PRO_ESTADO == 1)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('productos.edit', $prod->PRO_ID) }}" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                        
                        <form action="{{ route('productos.destroy', $prod->PRO_ID) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('¿Eliminar producto {{ $prod->PRO_NOMBRE }}? Esto borrará también su stock en bodegas.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No existen productos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection