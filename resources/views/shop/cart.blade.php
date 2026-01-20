<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Compra - Cap & Knit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body>

<header class="bg-light border-bottom sticky-top py-3 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('shop.index') }}" class="text-dark fw-bold fs-4 text-decoration-none">
            <i class="bi bi-shop"></i> CAP & KNIT
        </a>
        <nav>
            <ul class="d-flex gap-4 list-unstyled mb-0 fw-bold align-items-center">
                <li><a href="{{ route('shop.products') }}" class="text-dark text-decoration-none">Productos</a></li>
                <li>
                    <a href="{{ route('shop.cart') }}" class="text-dark text-decoration-none">
                        <i class="bi bi-cart-fill"></i> Carrito ({{ count(session('cart', [])) }})
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<main class="container my-5">
    @if(session('success'))
        <div class="alert alert-success fw-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger fw-bold">{{ session('error') }}</div>
    @endif

    <h1 class="mb-4 fw-bold text-uppercase">Resumen de Compra</h1>

    @if(session('cart') && count(session('cart')) > 0)
        <div class="table-responsive">
            <table class="table table-bordered align-middle shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th style="width:35%">Producto</th>
                        <th style="width:15%">Precio Unitario</th>
                        <th style="width:20%" class="text-center">Cantidad</th>
                        <th style="width:15%" class="text-center">Subtotal</th>
                        <th style="width:15%" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $item)
                        @php
                            $subtotalItem = $item['price'] * $item['quantity'];
                            $total += $subtotalItem;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    {{-- Ajusta la ruta de la imagen según tu carpeta real --}}
                                    <img src="{{ asset('img/productos/gorranewera.jpg') }}"
                                         alt="{{ $item['name'] }}" class="rounded border" width="60" height="60"
                                         onerror="this.src='{{ asset('static/img/gorra_default.jpg') }}'">
                                    <strong>{{ $item['name'] }}</strong>
                                </div>
                            </td>
                            <td class="fw-bold">${{ number_format($item['price'], 2) }}</td>
                            <td class="text-center">
                                <form action="{{ route('update.cart') }}" method="POST" class="d-inline-flex align-items-center gap-2">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}" class="btn btn-outline-secondary btn-sm">−</button>
                                    <input type="text" class="form-control text-center fw-bold" value="{{ $item['quantity'] }}" readonly style="width:60px">
                                    <button type="submit" name="quantity" value="{{ min(10, $item['quantity'] + 1) }}" class="btn btn-outline-secondary btn-sm">+</button>
                                </form>
                            </td>
                            <td class="text-center fw-bold text-success">${{ number_format($subtotalItem, 2) }}</td>
                            <td class="text-center">
                                <form action="{{ route('remove.from.cart') }}" method="POST" onsubmit="return confirm('¿Eliminar producto?');">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $id }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5">
                            <div class="row justify-content-end p-4">
                                <div class="col-md-5 border rounded p-3">
                                    <h5 class="fw-bold mb-3">Detalle de Pago</h5>
                                    @php
                                        $iva = $total * 0.15;
                                        $totalPagar = $total + $iva;
                                    @endphp
                                    <div class="d-flex justify-content-between"><span>Subtotal:</span><span>${{ number_format($total, 2) }}</span></div>
                                    <div class="d-flex justify-content-between"><span>IVA (15%):</span><span>${{ number_format($iva, 2) }}</span></div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Total a Pagar:</span><span class="text-success">${{ number_format($totalPagar, 2) }}</span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <a href="{{ route('shop.products') }}" class="btn btn-outline-dark fw-bold">Seguir Comprando</a>
                                        
                                        {{-- 🛑 ESTA ES LA PARTE CRÍTICA QUE CONECTA CON LA FACTURA 🛑 --}}
                                        <form action="{{ route('shop.comprar') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success fw-bold">Procesar Compra</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="text-center py-5 border rounded shadow-sm bg-light">
            <i class="bi bi-cart-x fs-1 text-muted"></i>
            <h3 class="fw-bold mt-3">Tu carrito está vacío</h3>
            <a href="{{ route('shop.products') }}" class="btn btn-dark mt-3 px-5 fw-bold">Ir a la Tienda</a>
        </div>
    @endif
</main>

<footer class="bg-light border-top py-4 mt-5">
    <div class="container text-center fw-bold">&copy; {{ date('Y') }} Cap & Knit.</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>