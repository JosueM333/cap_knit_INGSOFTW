@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-uppercase">Gestión de Carritos</h1>

    <a href="{{ route('carritos.create') }}"
       class="btn btn-dark text-uppercase small">
        + Crear Carrito
    </a>
</div>

{{-- BUSCADOR --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="{{ route('carritos.index') }}" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Buscar por cédula o correo del cliente..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit"
                        class="btn btn-outline-dark w-100">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- TABLA --}}
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Subtotal</th>
                    <th>Impuesto</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse($carritos as $crd)
                <tr>
                    {{-- ID --}}
                    <td>{{ $crd->CRD_ID }}</td>

                    {{-- CLIENTE (defensivo) --}}
                    <td>
                        @if($crd->cliente)
                            {{ $crd->cliente->CLI_APELLIDOS }}
                            {{ $crd->cliente->CLI_NOMBRES }} <br>
                            <small class="text-muted">
                                {{ $crd->cliente->CLI_CEDULA }}
                            </small>
                        @else
                            <span class="text-muted">Sin cliente</span>
                        @endif
                    </td>

                    {{-- FECHA --}}
                    <td>
                        {{ $crd->CRD_FECHA_CREACION ?? '-' }}
                    </td>

                    {{-- TOTALES --}}
                    <td>${{ number_format($crd->CRD_SUBTOTAL ?? 0, 2) }}</td>
                    <td>${{ number_format($crd->CRD_IMPUESTO ?? 0, 2) }}</td>
                    <td>
                        <strong>
                            ${{ number_format($crd->CRD_TOTAL ?? 0, 2) }}
                        </strong>
                    </td>

                    {{-- ESTADO --}}
                    <td>
                        @switch($crd->CRD_ESTADO)
                            @case('ABIERTO')
                                <span class="badge bg-warning text-dark">
                                    Abierto
                                </span>
                                @break

                            @case('CERRADO')
                                <span class="badge bg-success">
                                    Cerrado
                                </span>
                                @break

                            @case('ACTIVO')
                                <span class="badge bg-primary">
                                    Activo
                                </span>
                                @break

                            @default
                                <span class="badge bg-secondary">
                                    {{ $crd->CRD_ESTADO }}
                                </span>
                        @endswitch
                    </td>

                    {{-- ACCIONES --}}
                    <td class="text-end">
                        <a href="{{ route('carritos.edit', $crd->CRD_ID) }}"
                           class="btn btn-sm btn-outline-primary me-1">
                            Editar
                        </a>

                        <form action="{{ route('carritos.destroy', $crd->CRD_ID) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('¿Desea eliminar este carrito?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                Borrar
                            </button>
                        </form>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8"
                        class="text-center py-4 text-muted">
                        No se encontraron carritos.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
