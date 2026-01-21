@extends('layouts.app')

@section('content')

<div class="mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
    <h1 class="h3 text-uppercase">Detalle de Orden #{{ $orden->ORD_ID }}</h1>
    <a href="{{ route('ordenes.index') }}" class="btn btn-outline-dark btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
    </a>
</div>

<div class="row g-4">
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-info-circle me-1"></i> Información General
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <small class="text-muted d-block">Fecha de Emisión</small>
                    <span class="fw-bold fs-5">{{ \Carbon\Carbon::parse($orden->ORD_FECHA)->format('d/m/Y') }}</span>
                    <br>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($orden->ORD_FECHA)->format('H:i A') }}</small>
                </div>
                <div class="list-group-item">
                    <small class="text-muted d-block">Estado</small>
                    @if($orden->ORD_ESTADO == 'PENDIENTE')
                        <span class="badge bg-warning text-dark w-100 py-2">PENDIENTE</span>
                    @elseif($orden->ORD_ESTADO == 'ANULADA')
                        <span class="badge bg-danger w-100 py-2">ANULADA</span>
                    @else
                        <span class="badge bg-secondary w-100 py-2">{{ $orden->ORD_ESTADO }}</span>
                    @endif
                </div>
                <div class="list-group-item">
                    <small class="text-muted d-block">Total Global</small>
                    <span class="text-primary fw-bold fs-4">$ {{ number_format($orden->ORD_TOTAL, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-shop me-1"></i> Datos del Proveedor
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $orden->proveedor->PRV_NOMBRE }}</h5>
                <p class="card-text mb-1"><i class="bi bi-card-heading me-2 text-muted"></i> RUC: {{ $orden->proveedor->PRV_RUC }}</p>
                <p class="card-text mb-1"><i class="bi bi-telephone me-2 text-muted"></i> {{ $orden->proveedor->PRV_TELEFONO }}</p>
                <p class="card-text mb-0"><i class="bi bi-geo-alt me-2 text-muted"></i> {{ $orden->proveedor->PRV_DIRECCION }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary"><i class="bi bi-box-seam me-1"></i> Productos Solicitados</h5>
                <span class="badge bg-light text-dark border">
                    {{ $orden->detalles->count() }} Ítems
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orden->detalles as $detalle)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $detalle->producto->PRO_NOMBRE }}</div>
                                <div class="small text-muted">Cód: {{ $detalle->producto->PRO_CODIGO }}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-dark rounded-pill px-3">{{ $detalle->DOR_CANTIDAD }}</span>
                            </td>
                            <td class="text-end">
                                $ {{ number_format($detalle->DOR_PRECIO, 2) }}
                            </td>
                            <td class="text-end fw-bold text-dark">
                                $ {{ number_format($detalle->DOR_SUBTOTAL, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end text-uppercase small text-muted">Total Final:</td>
                            <td class="text-end fw-bold fs-5">$ {{ number_format($orden->ORD_TOTAL, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection