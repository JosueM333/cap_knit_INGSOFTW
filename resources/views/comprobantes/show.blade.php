@extends('layouts.app')

@section('content')
    <div class="container py-5">

        <div class="d-flex justify-content-between mb-4 no-print">
            <a href="{{ route('comprobantes.index') }}" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left"></i> Volver a Gestión
            </a>
            <button onclick="window.print()" class="btn btn-primary fw-bold">
                <i class="bi bi-printer"></i> Imprimir Comprobante
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success no-print">{{ session('success') }}</div>
        @endif

        <div class="card border-dark shadow-sm p-4 bg-white" id="invoice">

            <div class="row align-items-start mb-5">
                {{-- NIVEL 1: TOTAL A PAGAR (El más importante) --}}
                <div class="col-12 text-end mb-4 border-bottom pb-4">
                    <small class="text-uppercase text-muted fw-bold ls-2">Total a Pagar</small>
                    <div class="display-3 fw-bolder text-success">
                        ${{ number_format($comprobante->COM_TOTAL, 2) }}
                    </div>
                </div>

                {{-- NIVEL 2: Datos de Factura y Cliente --}}
                <div class="col-md-7">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small bg-light p-2 rounded">Facturado a:</h6>
                    <div class="px-2">
                        <h4 class="fw-bold text-dark mb-1">{{ $comprobante->cliente->CLI_NOMBRES }}
                            {{ $comprobante->cliente->CLI_APELLIDOS }}
                        </h4>
                        <p class="mb-0 text-secondary">
                            <span class="fw-bold text-dark">ID:</span> {{ $comprobante->cliente->CLI_CEDULA }}<br>
                            <span class="fw-bold text-dark">Dirección:</span> {{ $comprobante->cliente->CLI_DIRECCION }}<br>
                            <span class="fw-bold text-dark">Tel:</span> {{ $comprobante->cliente->CLI_TELEFONO }}
                        </p>
                    </div>
                </div>

                <div class="col-md-5 text-end">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small bg-light p-2 rounded">Detalles del Documento
                    </h6>
                    <div class="px-2">
                        <div class="fs-4 fw-bold text-dark">FACTURA No.
                            {{ str_pad($comprobante->COM_ID, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="text-muted">
                            <span class="fw-bold">Fecha de Emisión:</span>
                            {{ \Carbon\Carbon::parse($comprobante->COM_FECHA)->format('d/m/Y') }}
                        </div>
                        <div class="mt-2">
                            @if($comprobante->COM_ESTADO == 'ANULADO')
                                <span class="badge bg-danger rounded-pill px-3">ANULADO</span>
                            @else
                                <span class="badge bg-success rounded-pill px-3">{{ $comprobante->COM_ESTADO }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-striped border-bottom">
                    <thead class="bg-dark text-white text-center">
                        <tr>
                            <th class="py-3">CANT</th>
                            <th class="py-3 text-start">DESCRIPCIÓN</th>
                            <th class="py-3 text-end">PRECIO UNIT.</th>
                            <th class="py-3 text-end">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comprobante->carrito->detalles as $detalle)
                            <tr>
                                <td class="text-center align-middle">{{ $detalle->DCA_CANTIDAD }}</td>
                                <td class="align-middle">
                                    <span class="fw-bold">{{ $detalle->producto->PRO_NOMBRE }}</span>
                                    <br><small class="text-muted">Código: {{ $detalle->producto->PRO_CODIGO }}</small>
                                </td>
                                <td class="text-end align-middle">${{ number_format($detalle->DCA_PRECIO_UNITARIO, 2) }}</td>
                                <td class="text-end align-middle fw-bold">
                                    ${{ number_format($detalle->DCA_CANTIDAD * $detalle->DCA_PRECIO_UNITARIO, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end mb-5">
                <div class="col-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-bold">${{ number_format($comprobante->COM_SUBTOTAL, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                        <span class="text-muted">IVA (15%):</span>
                        <span class="fw-bold">${{ number_format($comprobante->COM_IVA, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="fw-bold text-uppercase text-muted">Total:</small>
                        <span class="fw-bold text-success fs-5">${{ number_format($comprobante->COM_TOTAL, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- NIVEL 4: Logo y Datos de la Empresa --}}
            <div class="mt-auto border-top pt-4 text-center">
                <h5 class="fw-bold text-uppercase text-secondary mb-1 ls-2">Cap & Knit</h5>
                <p class="small text-muted mb-0">Tienda en Línea | RUC: 1799999999001 | ventas@capandknit.com</p>
                @if($comprobante->COM_OBSERVACIONES)
                    <p class="small text-muted mt-2 fst-italic">Observaciones: {{ $comprobante->COM_OBSERVACIONES }}</p>
                @endif
            </div>

        </div>
    </div>

    <style>
        @media print {
            @page {
                size: auto;
                margin: 5mm;
            }

            html,
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                width: 100%;
                height: 100%;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Ocultar elementos de navegación/interface */
            .no-print,
            .d-print-none,
            header,
            footer,
            .navbar,
            .btn {
                display: none !important;
            }

            /* Reset de container */
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Forzar layout de columnas (Facturado a / Detalles) */
            .col-md-7 {
                width: 58% !important;
                float: left;
            }

            .col-md-5 {
                width: 41% !important;
                float: right;
            }

            /* Asegurar que el row no tenga márgenes negativos que causen scroll */
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
                display: flex !important;
            }

            /* Tarjeta principal limpia */
            .card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            #invoice {
                box-shadow: none !important;
                border: none !important;
            }

            /* Colores y fondos */
            .bg-dark {
                background-color: #212529 !important;
                color: white !important;
            }

            .bg-light {
                background-color: #f8f9fa !important;
            }

            .badge {
                border: 1px solid #000;
                color: black !important;
            }
        }
    </style>
@endsection