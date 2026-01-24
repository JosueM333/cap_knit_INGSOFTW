@extends('layouts.app')

@section('content')
    {{-- BREADCRUMBS --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Admin</a></li>
            <li class="breadcrumb-item active" aria-current="page">Productos</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-uppercase">Gestión de Productos</h1>
        <a href="{{ route('productos.create') }}" class="btn btn-dark text-uppercase small">
            + Nuevo Producto
        </a>
    </div>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('productos.index') }}" method="GET" class="row g-2">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por Código SKU o Nombre..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-dark w-100">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary w-100"
                        title="Limpiar filtros">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">Img</th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Proveedor</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $prod)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset('static/img/gorra_default.jpg') }}" alt="Thumb" class="img-thumbnail"
                                    width="50" height="50">
                            </td>
                            <td class="font-monospace">{{ $prod->PRO_CODIGO }}</td>
                            <td>
                                <div class="fw-bold">{{ $prod->PRO_NOMBRE }}</div>
                                <small class="text-muted">
                                    {{ $prod->PRO_MARCA ?? 'S/M' }}
                                    @if($prod->PRO_COLOR) | {{ $prod->PRO_COLOR }} @endif
                                    @if($prod->PRO_TALLA) | Talla: {{ $prod->PRO_TALLA }} @endif
                                </small>
                            </td>
                            <td class="fw-bold text-success">${{ number_format($prod->PRO_PRECIO, 2) }}</td>
                            <td>{{ $prod->proveedor->PRV_NOMBRE }}</td>
                            <td class="text-end">
                                <a href="{{ route('productos.edit', $prod->PRO_ID) }}"
                                    class="btn btn-sm btn-outline-primary me-1">Editar</a>

                                {{-- Formulario de Borrado Físico --}}
                                {{-- Botón Trigger Modal de Borrado --}}
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal" data-action="{{ route('productos.destroy', $prod->PRO_ID) }}"
                                    data-item-name="{{ $prod->PRO_NOMBRE }}">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No existen productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection