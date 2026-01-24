@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Gestión de Facturas</h1>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Menú
            </a>
        </div>

        {{-- MENSAJES DE FEEDBACK --}}
        @if(session('info'))
            <div class="alert alert-info border-info text-center fw-bold mb-4"><i class="bi bi-info-circle me-2"></i>
                {{ session('info') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success border-success text-center fw-bold mb-4"><i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger border-danger text-center fw-bold mb-4">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error) <li><i class="bi bi-exclamation-triangle me-2"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- BUSCADOR --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                <h5 class="fw-bold text-primary"><i class="bi bi-search me-2"></i>Buscar Factura</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form action="{{ route('comprobantes.buscar') }}" method="POST" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-9">
                        <label for="criterio" class="form-label fw-bold small text-muted">Criterio de Búsqueda:</label>
                        <input type="text" name="criterio" id="criterio" class="form-control form-control-lg"
                            placeholder="Ingrese Número de Factura o Cédula..." required>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="bi bi-search me-1"></i>
                            Buscar</button>
                    </div>
                </form>
                @if(request()->routeIs('comprobantes.buscar'))
                    <div class="mt-3"><a href="{{ route('comprobantes.index') }}" class="text-decoration-none fw-bold"><i
                                class="bi bi-arrow-counterclockwise"></i> Ver todos los registros</a></div>
                @endif
            </div>
        </div>

        {{-- ZONA DE ACCIÓN PRINCIPAL --}}
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body p-4 text-center border rounded d-flex justify-content-between align-items-center">
                <div class="text-start">
                    <h5 class="fw-bold mb-1">Nueva Emisión</h5>
                    <p class="text-muted small mb-0">Generar factura para ventas pendientes.</p>
                </div>
                <a href="{{ route('comprobantes.create') }}" class="btn btn-success btn-lg fw-bold px-4"><i
                        class="bi bi-plus-circle me-2"></i> Nueva Factura</a>
            </div>
        </div>

        {{-- TABLA DE RESULTADOS --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Historial de Facturas Emitidas</h5>
                <span class="badge bg-light text-dark">{{ count($comprobantes) }} Registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nro</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-end pe-4" style="min-width: 250px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comprobantes as $comp)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ str_pad($comp->COM_ID, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($comp->COM_FECHA)->format('d/m/Y') }}</td>
                                    <td>
                                        {{ $comp->cliente->CLI_NOMBRES }} {{ $comp->cliente->CLI_APELLIDOS }}
                                        <br><small class="text-muted">ID: {{ $comp->cliente->CLI_CEDULA }}</small>
                                    </td>
                                    <td class="fw-bold">${{ number_format($comp->COM_TOTAL, 2) }}</td>
                                    <td>
                                        @if($comp->COM_ESTADO == 'ANULADO')
                                            <span class="badge bg-danger">ANULADO</span>
                                        @else
                                            <span class="badge bg-success">{{ $comp->COM_ESTADO }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            {{-- Ver --}}
                                            <a href="{{ route('comprobantes.show', $comp->COM_ID) }}"
                                                class="btn btn-sm btn-dark fw-bold">
                                                Ver
                                            </a>

                                            @if($comp->COM_ESTADO != 'ANULADO')
                                                {{-- Editar --}}
                                                <a href="{{ route('comprobantes.edit', $comp->COM_ID) }}"
                                                    class="btn btn-sm btn-primary fw-bold">
                                                    Editar
                                                </a>

                                                {{-- Anular (Modal) --}}
                                                <button type="button" class="btn btn-sm btn-danger fw-bold"
                                                    onclick="abrirModalAnulacion('{{ route('comprobantes.anular', $comp->COM_ID) }}', '{{ str_pad($comp->COM_ID, 6, '0', STR_PAD_LEFT) }}')">
                                                    Anular
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">No hay facturas emitidas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE ANULACIÓN --}}
    <div class="modal fade" id="modalAnular" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-danger">
                <form id="formAnular" action="" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Anular
                            Comprobante</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Está a punto de anular el Comprobante Nro: <strong id="modal_nro_factura"></strong>.</p>
                        <p class="text-danger small">Esta acción es irreversible (Borrado Lógico).</p>

                        <div class="mb-3">
                            <label for="motivo_anulacion" class="form-label fw-bold">Motivo de la anulación:</label>
                            <textarea class="form-control" name="motivo_anulacion" rows="3" required
                                placeholder="Ej: Error en datos del cliente, Devolución total..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger fw-bold">Confirmar Anulación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalAnulacion(urlAccion, nroFactura) {
            // Asignar la ruta generada por Blade al formulario
            document.getElementById('formAnular').action = urlAccion;
            // Mostrar número visual
            document.getElementById('modal_nro_factura').textContent = '#' + nroFactura;
            // Abrir modal
            var myModal = new bootstrap.Modal(document.getElementById('modalAnular'));
            myModal.show();
        }
    </script>
@endsection