@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    {{-- Botones de Navegación --}}
    <div class="d-flex justify-content-between mb-4 no-print">
        <a href="{{ route('comprobantes.create') }}" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left"></i> Volver a Gestión
        </a>
        <button onclick="window.print()" class="btn btn-primary fw-bold">
            <i class="bi bi-printer"></i> Imprimir Comprobante
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success no-print">{{ session('success') }}</div>
    @endif

    {{-- Diseño de la Factura --}}
    <div class="card border-dark shadow-sm p-4" id="invoice">
        
        {{-- Cabecera --}}
        <div class="row mb-4 border-bottom pb-3">
            <div class="col-8">
                <h2 class="fw-bold text-uppercase">Mi Tienda de Gorras</h2>
                <p class="mb-0">Dirección de la Tienda, Quito - Ecuador</p>
                <p class="mb-0">RUC: 1799999999001</p>
                <p>Teléfono: (02) 222-2222</p>
            </div>
            <div class="col-4 text-end">
                <h4 class="text-primary fw-bold">FACTURA</h4>
                <h5 class="text-muted">No. {{ str_pad($comprobante->COM_ID, 6, '0', STR_PAD_LEFT) }}</h5>
                <p class="mb-0"><strong>Fecha:</strong> {{ $comprobante->COM_FECHA }}</p>
                <p><strong>Estado:</strong> <span class="badge bg-success">{{ $comprobante->COM_ESTADO }}</span></p>
            </div>
        </div>

        {{-- Datos del Cliente --}}
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="fw-bold bg-light p-2 border">Datos del Cliente</h5>
                <div class="px-2">
                    <p class="mb-1"><strong>Razón Social:</strong> {{ $comprobante->cliente->CLI_NOMBRES }} {{ $comprobante->cliente->CLI_APELLIDOS }}</p>
                    <p class="mb-1"><strong>Cédula/RUC:</strong> {{ $comprobante->cliente->CLI_CEDULA }}</p>
                    <p class="mb-1"><strong>Dirección:</strong> {{ $comprobante->cliente->CLI_DIRECCION }}</p>
                    <p class="mb-1"><strong>Teléfono:</strong> {{ $comprobante->cliente->CLI_TELEFONO }}</p>
                </div>
            </div>
        </div>

        {{-- Tabla de Detalles --}}
        <div class="table-responsive mb-4">
            <table class="table table-bordered border-dark">
                <thead class="bg-dark text-white text-center">
                    <tr>
                        <th>CANT</th>
                        <th>DESCRIPCIÓN</th>
                        <th>PRECIO UNIT.</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comprobante->carrito->detalles as $detalle)
                    <tr>
                        <td class="text-center">{{ $detalle->DCA_CANTIDAD }}</td>
                        <td>
                            {{ $detalle->producto->PRO_NOMBRE }}
                            <br><small class="text-muted">Código: {{ $detalle->producto->PRO_CODIGO }}</small>
                        </td>
                        <td class="text-end">${{ number_format($detalle->DCA_PRECIO_UNITARIO, 2) }}</td>
                        <td class="text-end">${{ number_format($detalle->DCA_CANTIDAD * $detalle->DCA_PRECIO_UNITARIO, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totales --}}
        <div class="row">
            <div class="col-7">
                <div class="p-3 bg-light border rounded">
                    <strong>Observaciones:</strong>
                    <p class="mb-0">{{ $comprobante->COM_OBSERVACIONES ?? 'Ninguna.' }}</p>
                </div>
            </div>
            <div class="col-5">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-end"><strong>Subtotal:</strong></td>
                        <td class="text-end">${{ number_format($comprobante->COM_SUBTOTAL, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>IVA (15%):</strong></td>
                        <td class="text-end">${{ number_format($comprobante->COM_IVA, 2) }}</td>
                    </tr>
                    <tr class="border-top border-dark">
                        <td class="text-end fs-5"><strong>TOTAL:</strong></td>
                        <td class="text-end fs-5 fw-bold">${{ number_format($comprobante->COM_TOTAL, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-center mt-5 text-muted small">
            Gracias por su compra. Documento generado electrónicamente.
        </div>
    </div>
</div>

{{-- Estilos para impresión --}}
<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; shadow: none !important; }
        body { background-color: white; }
    }
</style>
@endsection