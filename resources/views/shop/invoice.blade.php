<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $comprobante->COM_ID }} - Cap & Knit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f8f9fa;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
            background: #fff;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .top-title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .item td {
            border-bottom: 1px solid #eee;
        }

        .item.last td {
            border-bottom: none;
        }

        .total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .invoice-box {
                box-shadow: none;
                border: 0;
            }
        }
    </style>
</head>

<body class="py-5">

    <div class="container text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-dark fw-bold btn-lg shadow">
            <i class="bi bi-printer"></i> Imprimir / Guardar como PDF
        </button>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Volver a la Tienda</a>
    </div>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="top-title fw-bold">
                                CAP & KNIT
                            </td>
                            <td>
                                Factura #: {{ str_pad($comprobante->COM_ID, 6, '0', STR_PAD_LEFT) }}<br>
                                Fecha: {{ \Carbon\Carbon::parse($comprobante->COM_FECHA)->format('d/m/Y') }}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Emisor:</strong><br>
                                Cap & Knit S.A.<br>
                                Quito, Ecuador<br>
                                RUC: 1799999999001
                            </td>
                            <td>
                                <strong>Cliente:</strong><br>
                                {{ $comprobante->cliente->CLI_NOMBRES }} {{ $comprobante->cliente->CLI_APELLIDOS }}<br>
                                {{ $comprobante->cliente->CLI_EMAIL }}<br>
                                {{ $comprobante->cliente->CLI_CEDULA }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item / Producto</td>
                <td>Precio</td>
            </tr>

            @foreach($comprobante->detalles as $detalle)
                <tr class="item">
                    <td>
                        {{ $detalle->producto->PRO_NOMBRE ?? 'Producto ID ' . $detalle->PRO_ID }}
                        x {{ $detalle->DCO_CANTIDAD }}
                    </td>
                    <td>
                        ${{ number_format($detalle->DCO_SUBTOTAL, 2) }}
                    </td>
                </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td>
                    Subtotal: ${{ number_format($comprobante->COM_SUBTOTAL, 2) }}<br>
                    IVA (15%): ${{ number_format($comprobante->COM_IVA, 2) }}<br>
                    <strong>Total: ${{ number_format($comprobante->COM_TOTAL, 2) }}</strong>
                </td>
            </tr>
        </table>

        <div class="mt-4 pt-4 border-top text-center small text-muted">
            <p>Gracias por su compra. Documento generado electrónicamente.</p>
        </div>
    </div>
</body>

</html>