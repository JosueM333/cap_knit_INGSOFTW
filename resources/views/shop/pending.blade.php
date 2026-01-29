@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5">Mis Pedidos Pendientes</h1>
        <p class="text-muted">Finaliza tus compras pendientes para asegurar el stock.</p>
    </div>

    @if($pedidos->isEmpty())
        <div class="alert alert-info text-center">
            No tienes pedidos pendientes por facturar. <a href="{{ route('shop.products') }}" class="fw-bold">Ir a la tienda</a>.
        </div>
    @else
        <div class="row">
            @foreach($pedidos as $pedido)
                <div class="col-md-10 mx-auto mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h5 class="mb-0 fw-bold text-primary">Pedido #{{ $pedido->CRD_ID }}</h5>
                                <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <span class="badge bg-warning text-dark">{{ $pedido->CRD_ESTADO }}</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <thead class="text-muted border-bottom">
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Cant.</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pedido->detalles as $detalle)
                                            <tr>
                                                <td>{{ $nombresProductos[$detalle->PRO_ID] ?? 'Producto ID '.$detalle->PRO_ID }}</td>
                                                <td class="text-center">{{ $detalle->DCA_CANTIDAD }}</td>
                                                <td class="text-end">${{ number_format($detalle->DCA_PRECIO_UNITARIO, 2) }}</td>
                                                <td class="text-end fw-bold">${{ number_format($detalle->DCA_SUBTOTAL, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="border-top">
                                        <tr>
                                            <td colspan="3" class="text-end">Subtotal:</td>
                                            <td class="text-end">${{ number_format($pedido->CRD_SUBTOTAL, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end">IVA (15%):</td>
                                            <td class="text-end">${{ number_format($pedido->CRD_IMPUESTO, 2) }}</td>
                                        </tr>
                                        <tr class="fs-5">
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="text-end fw-bold text-success">${{ number_format($pedido->CRD_TOTAL, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-end py-3">
                            <form action="{{ route('shop.processOrder', $pedido->CRD_ID) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success fw-bold px-4">
                                    <i class="bi bi-receipt-cutoff me-2"></i> Facturar y Finalizar Compra
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
