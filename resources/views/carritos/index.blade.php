@extends('layouts.app')

@section('content')

<div class="mb-4 border-bottom pb-2">
    <h1 class="h3 text-uppercase">Gestión de Carritos</h1>
</div>

{{-- BLOQUE DE MENSAJES --}}
@if(session('error'))
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- PANEL DE ACCIONES --}}
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            
            {{-- CASO F7.2: CONSULTAR CARRITOS --}}
            <div class="col-md-3">
                <a href="{{ route('carritos.consultar') }}" class="btn btn-dark w-100">
                    Consultar Carritos
                </a>
            </div>

            {{-- CASO F7.3: BUSCAR CARRITO (Solo Cédula/Correo) --}}
            <div class="col-md-9">
                <form action="{{ route('carritos.buscar_carrito') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <div class="flex-grow-1">
                        <input type="text" 
                               name="criterio_carrito" 
                               class="form-control" 
                               placeholder="Ingrese Cédula o Correo del cliente..." 
                               required>
                    </div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>
            
        </div>
    </div>
</div>

{{-- RESULTADOS --}}
@if(isset($carritosActivos))
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Listado de Carritos</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($carritosActivos as $carr)
                <tr>
                    <td>#{{ $carr->CRD_ID }}</td>
                    <td>
                        <strong>{{ $carr->cliente->CLI_APELLIDOS }} {{ $carr->cliente->CLI_NOMBRES }}</strong><br>
                        <small class="text-muted">{{ $carr->cliente->CLI_CEDULA }}</small>
                    </td>
                    <td class="text-end fw-bold">${{ number_format($carr->CRD_TOTAL, 2) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $carr->CRD_ESTADO == 'ACTIVO' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $carr->CRD_ESTADO }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="btn-group" role="group">
                            {{-- F7.4 Editar --}}
                            <a href="{{ route('carritos.editar', $carr->CRD_ID) }}" class="btn btn-sm btn-outline-primary">
                                Editar
                            </a>

                            {{-- F7.5 Vaciar --}}
                            <form action="{{ route('carritos.vaciar', $carr->CRD_ID) }}" method="POST" class="d-inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('ATENCIÓN: Esto eliminará permanentemente todos los productos de este carrito. ¿Desea continuar?')">
                                    Vaciar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <h5 class="text-muted">No se encontraron carritos activos.</h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection