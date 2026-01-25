@extends('layouts.app')

@section('content')

    {{-- ENCABEZADO --}}
    <div class="mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h3 text-uppercase">Gestión de Órdenes de Compra</h1>

        <a href="{{ route('ordenes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nueva Orden
        </a>
    </div>

    {{-- BUSCADOR --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <form action="{{ route('ordenes.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="fw-bold"><i class="bi bi-search"></i> Buscar:</label>
                </div>
                <div class="col-md-6">
                    <input type="text" name="criterio" class="form-control"
                        placeholder="Ingrese N° Orden o Nombre Proveedor..." value="{{ request('criterio') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-dark">Buscar</button>
                </div>

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

    {{-- MENSAJES --}}
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
                                {{-- CORRECCIÓN: Lógica actualizada a los nuevos estados --}}
                                @if($orden->ORD_ESTADO == 'PENDIENTE')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($orden->ORD_ESTADO == 'ANULADA')
                                    <span class="badge bg-danger">Anulada</span>
                                @else
                                    <span class="badge bg-secondary">{{ $orden->ORD_ESTADO }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    {{-- VER DETALLE --}}
                                    <a href="{{ route('ordenes.show', $orden->ORD_ID) }}" class="btn btn-sm btn-outline-info">
                                        Ver
                                    </a>

                                    {{-- ACCIONES DE PENDIENTE --}}
                                    @if($orden->ORD_ESTADO == 'PENDIENTE')
                                        <a href="{{ route('ordenes.edit', $orden->ORD_ID) }}" class="btn btn-sm btn-primary">
                                            Editar
                                        </a>
                                        {{-- BOTÓN RECIBIR --}}
                                        <button type="button" class="btn btn-sm btn-success"
                                            onclick="abrirModalRecibir({{ $orden->ORD_ID }})">
                                            Recibir
                                        </button>
                                    @endif

                                    {{-- ANULAR --}}
                                    @if($orden->ORD_ESTADO != 'ANULADA')
                                        <form action="{{ route('ordenes.destroy', $orden->ORD_ID) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Está seguro de ANULAR esta orden?');">
                                                Anular
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                    <h5>No existen órdenes de compra registradas</h5>
                                    <a href="{{ route('ordenes.create') }}" class="btn btn-primary btn-sm mt-2">
                                        Crear primera orden
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL RECIBIR ORDEN --}}
    <div class="modal fade" id="modalRecibir" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formRecibir" action="" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Recibir Mercadería</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Se registrará el ingreso de inventario para la Orden seleccionada.</p>

                        <div class="mb-3">
                            <label for="BOD_ID" class="form-label fw-bold">Seleccione Bodega de Destino:</label>
                            <select name="BOD_ID" class="form-select" required>
                                @foreach($bodegas as $bodega)
                                    <option value="{{ $bodega->BOD_ID }}">
                                        {{ $bodega->BOD_NOMBRE }} ({{ $bodega->BOD_UBICACION }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i> Confirmar Entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalRecibir(id) {
            let form = document.getElementById('formRecibir');
            // Actualizamos la acción del formulario dinámicamente
            form.action = "/ordenes/" + id + "/recibir";

            var myModal = new bootstrap.Modal(document.getElementById('modalRecibir'));
            myModal.show();
        }
    </script>

@endsection