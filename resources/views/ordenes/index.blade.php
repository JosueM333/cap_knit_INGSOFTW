@extends('layouts.app')

@section('content')

{{-- ENCABEZADO: Título y Botón Crear (F2.1) --}}
<div class="mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h1 class="h3 text-uppercase">Gestión de Órdenes de Compra</h1>
    
    {{-- Botón para disparar el Caso de Uso F2.1 --}}
    <a href="{{ route('ordenes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Nueva Orden
    </a>
</div>

{{-- F2.3 BUSCAR ORDEN DE COMPRA --}}
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body py-3">
        {{-- Formulario GET para filtrar --}}
        <form action="{{ route('ordenes.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="fw-bold"><i class="bi bi-search"></i> Buscar:</label>
            </div>
            <div class="col-md-6">
                {{-- F2.3 Paso 3: Ingreso del criterio --}}
                <input type="text" name="criterio" class="form-control" 
                       placeholder="Ingrese N° Orden o Nombre Proveedor..." 
                       value="{{ request('criterio') }}">
            </div>
            <div class="col-auto">
                {{-- F2.3 Paso 4: Botón Buscar --}}
                <button type="submit" class="btn btn-dark">Buscar</button>
            </div>
            
            {{-- Botón limpiar si hay búsqueda activa --}}
            @if(request('criterio'))
                <div class="col-auto">
                    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i> Limpiar
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- BLOQUE DE MENSAJES --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- TABLA DE RESULTADOS --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 text-muted">Historial de Adquisiciones</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th># ID</th>
                    <th>Proveedor</th>
                    <th>Fecha Emisión</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Estado</th>
                    {{-- Aumentamos el ancho para que quepan los botones de texto --}}
                    <th class="text-end" style="min-width: 250px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ordenes as $orden)
                <tr>
                    <td>
                        <strong>#{{ $orden->ORD_ID }}</strong>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-bold">{{ $orden->proveedor->PRV_NOMBRE ?? 'Proveedor Eliminado' }}</span>
                            <small class="text-muted">RUC: {{ $orden->proveedor->PRV_RUC ?? '--' }}</small>
                        </div>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($orden->ORD_FECHA)->format('d/m/Y H:i') }}
                    </td>
                    <td class="text-end fw-bold">
                        $ {{ number_format($orden->ORD_TOTAL, 2) }}
                    </td>
                    <td class="text-center">
                        @if($orden->ORD_ESTADO == 'A')
                            <span class="badge bg-success">Activa / Recibida</span>
                        @elseif($orden->ORD_ESTADO == 'P')
                             <span class="badge bg-warning text-dark">Pendiente</span>
                        @else
                            <span class="badge bg-danger">Anulada</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group" role="group">
                            {{-- VER DETALLE --}}
                            <a href="{{ route('ordenes.show', $orden->ORD_ID) }}" class="btn btn-sm btn-outline-info">
                                Ver
                            </a>

                            {{-- F2.4 MODIFICAR: Botón con TEXTO "Editar" --}}
                            @if($orden->ORD_ESTADO == 'P')
                                <a href="{{ route('ordenes.edit', $orden->ORD_ID) }}" class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>
                                    Editar
                                </button>
                            @endif

                            {{-- F2.5 ANULAR: Botón con TEXTO "Borrar" --}}
                            @if($orden->ORD_ESTADO != 'C')
                                <form action="{{ route('ordenes.destroy', $orden->ORD_ID) }}" method="POST" class="d-inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="return confirm('¿Está seguro de Borrar (anular) esta orden?');">
                                        Borrar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                {{-- MANEJO DE FLUJOS ALTERNOS DE VACÍO --}}
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-inbox display-4 d-block mb-3"></i>
                            
                            {{-- Mensaje dinámico según F2.3 o F2.2 --}}
                            <h5>
                                @if(request('criterio'))
                                    {{-- Flujo Alterno F2.3 --}}
                                    Orden de compra no localizada
                                @else
                                    {{-- Flujo Alterno F2.2 --}}
                                    No existen órdenes de compra registradas
                                @endif
                            </h5>

                            @if(!request('criterio'))
                                <p class="mb-3">Comience registrando una nueva adquisición.</p>
                                <a href="{{ route('ordenes.create') }}" class="btn btn-primary btn-sm">
                                    Crear primera orden
                                </a>
                            @else
                                <p class="mb-3">Intente con otro número o proveedor.</p>
                                <a href="{{ route('ordenes.index') }}" class="btn btn-secondary btn-sm">
                                    Limpiar Búsqueda
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection