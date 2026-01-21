@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">Nueva Factura / Comprobante</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Menú
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Ventas Pendientes de Facturar</h5>
        </div>
        <div class="card-body">
            @if($ventasPendientes->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">No hay ventas pendientes de facturación.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th># Pedido</th>
                                <th>Fecha Venta</th>
                                <th>Cliente</th>
                                <th>Total Venta</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasPendientes as $venta)
                            <tr>
                                <td><strong>#{{ $venta->CRD_ID }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $venta->cliente->CLI_NOMBRES }} {{ $venta->cliente->CLI_APELLIDOS }}<br>
                                    <small class="text-muted">{{ $venta->cliente->CLI_CEDULA }}</small>
                                </td>
                                <td class="fw-bold">${{ number_format($venta->CRD_TOTAL, 2) }}</td>
                                <td class="text-end">
                                    <button type="button" 
                                            class="btn btn-primary btn-sm fw-bold"
                                            onclick="abrirModalFacturacion({{ $venta->CRD_ID }}, '{{ $venta->cliente->CLI_NOMBRES }}', {{ $venta->CRD_SUBTOTAL }})">
                                        <i class="bi bi-receipt"></i> Facturar
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL FACTURACIÓN --}}
<div class="modal fade" id="modalFacturar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('comprobantes.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Emitir Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="CRD_ID" id="modal_crd_id">

                    <p>Facturando a: <strong id="modal_cliente_nombre"></strong></p>
                    
                    <div class="alert alert-info py-2">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>$<span id="modal_subtotal">0.00</span></span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold text-primary">
                            <span>IVA (15%):</span>
                            <span>$<span id="modal_iva">0.00</span></span>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total a Pagar:</span>
                            <span>$<span id="modal_total">0.00</span></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones (Opcional)</label>
                        <textarea class="form-control" name="observaciones" rows="2" placeholder="Ej: Pago en efectivo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="bi bi-check-circle me-1"></i> Emitir Factura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function abrirModalFacturacion(id, nombre, subtotal) {
        document.getElementById('modal_crd_id').value = id;
        document.getElementById('modal_cliente_nombre').textContent = nombre;

        let iva = subtotal * 0.15;
        let total = subtotal + iva;

        document.getElementById('modal_subtotal').textContent = parseFloat(subtotal).toFixed(2);
        document.getElementById('modal_iva').textContent = iva.toFixed(2);
        document.getElementById('modal_total').textContent = total.toFixed(2);

        var myModal = new bootstrap.Modal(document.getElementById('modalFacturar'));
        myModal.show();
    }
</script>
@endsection