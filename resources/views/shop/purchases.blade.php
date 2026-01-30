@extends('layouts.shop')

@section('content')
    <div class="container py-5">
        <h1 class="mb-4 fw-bold text-uppercase h3">Mis Compras</h1>

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nro Factura</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comprobantes as $comp)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ str_pad($comp->COM_ID, 6, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($comp->COM_FECHA)->format('d/m/Y') }}</td>
                                    <td class="fw-bold">${{ number_format($comp->COM_TOTAL, 2) }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $comp->COM_ESTADO }}</span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('shop.invoice', $comp->COM_ID) }}"
                                            class="btn btn-sm btn-dark fw-bold">
                                            <i class="bi bi-eye-fill me-1"></i> Ver Factura
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="bi bi-bag-x fs-1 d-block mb-3"></i>
                                        Aún no has realizado ninguna compra.
                                        <div class="mt-3">
                                            <a href="{{ route('shop.products') }}" class="btn btn-outline-dark fw-bold">
                                                Ir a la Tienda
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection