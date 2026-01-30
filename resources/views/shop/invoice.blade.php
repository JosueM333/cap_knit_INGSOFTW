@extends('layouts.invoice')

@section('content')
    <div class="container py-5 animate-entry">

        <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
            <a href="{{ route('shop.index') }}" class="btn btn-outline-dark fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Volver a la Tienda
            </a>
            <button onclick="window.print()" class="btn btn-dark fw-bold shadow">
                <i class="bi bi-printer-fill me-1"></i> Imprimir Comprobante / Guardar PDF
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success fw-bold border-2 border-success shadow-sm mb-4 d-print-none" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- FACTURA --}}
        <div class="card border-dark shadow-lg p-5 bg-white" id="invoice-doc">

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

            {{-- NIVEL 3: Tabla de Detalles --}}
            <div class="table-responsive mb-5">
                <table class="table table-striped border-bottom">
                    <thead class="bg-dark text-white text-uppercase small text-center" style="letter-spacing: 1px;">
                        <tr>
                            <th style="width: 10%" class="py-3">Cant.</th>
                            <th style="width: 50%" class="text-start py-3">Descripción</th>
                            <th style="width: 20%" class="text-end py-3">Precio Unit.</th>
                            <th style="width: 20%" class="text-end py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comprobante->carrito->detalles as $detalle)
                            <tr>
                                <td class="text-center fw-bold align-middle">{{ $detalle->DCA_CANTIDAD }}</td>
                                <td class="align-middle">
                                    <span class="fw-bold text-dark">{{ $detalle->producto->PRO_NOMBRE }}</span>
                                    <br>
                                    <small class="text-muted">Cód: {{ $detalle->producto->PRO_CODIGO }}</small>
                                </td>
                                <td class="text-end align-middle">${{ number_format($detalle->DCA_PRECIO_UNITARIO, 2) }}</td>
                                <td class="text-end fw-bold align-middle">
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
                    {{-- Total repetido pequeño por consistencia --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="fw-bold text-uppercase text-muted">Total:</small>
                        <span class="fw-bold text-success fs-5">${{ number_format($comprobante->COM_TOTAL, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- NIVEL 4: Logo y Datos de la Empresa (Al final, sutil) --}}
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
                /* Margen pequeño para seguridad */
            }

            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Ocultar navegación */
            .d-print-none,
            header,
            footer,
            .accessibility-widget {
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
            /* Bootstrap col-md-* a veces colapsa en impresión, forzamos anchos */
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
                /* Mantener flexbox */
            }

            /* Tarjeta principal limpia */
            #invoice-doc {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            /* Textos y badgets */
            .badge {
                border: 1px solid #000;
                /* Borde para que se vea si no imprime fondo */
                color: black !important;
            }

            .bg-light {
                background-color: #f8f9fa !important;
            }
        }
    </style>

    <script>
        // Auto-imprimir al cargar la página para dar la sensación de "ventana flotante" de factura
        window.onload = function () {
            // Pequeño retardo para asegurar que los estilos carguen
            setTimeout(function () {
                window.print();
            }, 500);
        };
    </script>
@endsection