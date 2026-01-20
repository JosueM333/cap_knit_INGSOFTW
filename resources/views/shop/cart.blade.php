<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Cap & Knit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>

<body>

    <a href="#main-content" class="skip-link">Saltar al contenido</a>

    <header class="u-bg-gray border-bottom border-2 sticky-top py-3 shadow-sm" role="banner">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('shop.index') }}" class="text-dark fw-bold fs-4 text-decoration-none d-flex align-items-center">
                <span class="logo-text">CAP & KNIT</span>
            </a>
            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled mb-0 fw-bold align-items-center text-uppercase small">
                    <li>
                        <a href="{{ route('shop.products') }}" class="text-dark text-decoration-none hover-border-dark">Productos</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cart') }}" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1" aria-current="page">
                            <i class="bi bi-cart-fill" aria-hidden="true"></i> 
                            Carrito ({{ count(session('cart', [])) }})
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" class="container my-5" tabindex="-1">
        
        @if(session('success'))
            <div class="alert alert-success fw-bold border-2 border-success shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i> {{ session('success') }}
            </div>
        @endif

        <h1 class="mb-4 fw-bold text-uppercase display-5">Tu Carrito</h1>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="table-responsive">
                <table class="table table-bordered align-middle shadow-sm">
                    <thead class="table-light border-dark">
                        <tr>
                            <th scope="col" style="width:35%">Producto</th>
                            <th scope="col" style="width:15%">Precio</th>
                            <th scope="col" style="width:20%" class="text-center">Cantidad</th>
                            <th scope="col" style="width:15%" class="text-center">Subtotal</th>
                            <th scope="col" style="width:15%" class="text-center">Borrar</th>
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
                                        <img src="{{ asset('img/productos/gorranewera.jpg') }}"
                                             alt="" 
                                             class="rounded border border-dark" 
                                             width="60" height="60"
                                             aria-hidden="true"
                                             onerror="this.src='{{ asset('static/img/gorra_default.jpg') }}'">
                                        <div class="fw-bold">{{ $item['name'] }}</div>
                                    </div>
                                </td>

                                <td class="fw-bold">
                                    <span class="visually-hidden">Precio unitario </span>
                                    ${{ number_format($item['price'], 2) }}
                                </td>

                                <td class="text-center">
                                    <form action="{{ route('update.cart') }}" method="POST" class="d-inline-flex align-items-center gap-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        
                                        <button type="submit" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}" 
                                                class="btn btn-outline-dark btn-sm fw-bold px-3"
                                                aria-label="Menos {{ $item['name'] }}">
                                            <span aria-hidden="true">−</span>
                                        </button>
                                        
                                        <label for="qty-{{ $id }}" class="visually-hidden">Cantidad</label>
                                        <input type="text" id="qty-{{ $id }}" 
                                               class="form-control text-center fw-bold border-dark mx-1" 
                                               value="{{ $item['quantity'] }}" readonly style="width:50px">
                                        
                                        <button type="submit" name="quantity" value="{{ min(10, $item['quantity'] + 1) }}" 
                                                class="btn btn-outline-dark btn-sm fw-bold px-3"
                                                aria-label="Más {{ $item['name'] }}">
                                            <span aria-hidden="true">+</span>
                                        </button>
                                    </form>
                                </td>

                                <td class="text-center fw-bold text-success">
                                    <span class="visually-hidden">Subtotal </span>
                                    ${{ number_format($subtotalItem, 2) }}
                                </td>

                                <td class="text-center">
                                    <form action="{{ route('remove.from.cart') }}" method="POST">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm border-2"
                                                aria-label="Quitar {{ $item['name'] }}"
                                                onclick="return confirm('¿Quitar del carrito?');">
                                            <i class="bi bi-trash-fill" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="p-0 border-0">
                                <div class="row justify-content-end p-4">
                                    <div class="col-md-5 border border-2 border-dark rounded p-4 bg-white shadow-sm">
                                        <h2 class="h5 fw-bold mb-3 border-bottom border-dark pb-2">Resumen</h2>
                                        @php
                                            $iva = $total * 0.15;
                                            $totalPagar = $total + $iva;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span class="fw-bold">${{ number_format($total, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>IVA (15%):</span>
                                            <span class="fw-bold">${{ number_format($iva, 2) }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between fw-bold fs-4 bg-light p-2 border border-dark rounded">
                                            <span>Total:</span>
                                            <span class="text-success">${{ number_format($totalPagar, 2) }}</span>
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-4">
                                            <form action="{{ route('shop.comprar') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success fw-bold w-100 btn-lg shadow border-2 border-success text-uppercase">
                                                    Pagar Compra
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('shop.products') }}" class="btn btn-outline-dark fw-bold w-100">
                                                Seguir Comprando
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5 border border-2 border-dark rounded shadow-sm bg-light">
                <i class="bi bi-cart-x fs-1 text-muted" aria-hidden="true"></i>
                <h2 class="fw-bold mt-3 h3">Carrito vacío</h2>
                <a href="{{ route('shop.products') }}" class="btn btn-dark px-5 fw-bold shadow border-2 mt-3">
                    Ver Productos
                </a>
            </div>
        @endif
    </main>

    <footer class="u-bg-gray border-top border-2 border-dark py-5" role="contentinfo">
        <div class="container text-center text-md-start">
            <p class="mb-0 fw-bold">&copy; {{ date('Y') }} Cap & Knit.</p>
        </div>
    </footer>

    <div class="accessibility-widget">
        <div id="access-menu" class="access-menu p-3" hidden>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h2 class="h6 fw-bold mb-0">Opciones</h2>
                <button type="button" class="btn-close" aria-label="Cerrar" onclick="document.getElementById('access-btn').click()"></button>
            </div>
            
            <div class="mb-3">
                <span class="d-block small fw-bold mb-2">Tamaño texto</span>
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-decrease-font" aria-label="Achicar letra">A-</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-reset-font" aria-label="Normal">A</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-increase-font" aria-label="Agrandar letra">A+</button>
                </div>
            </div>

            <div class="mb-3">
                <button id="contrast-toggle-widget" class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Contraste</span>
                    <i class="bi bi-circle-half" aria-hidden="true"></i>
                </button>
            </div>

            <div class="mb-1">
                <button id="links-toggle" class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Subrayar</span>
                    <i class="bi bi-type-underline" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <button id="access-btn" class="btn btn-dark rounded-circle shadow-lg p-3 mt-2" 
                aria-label="Accesibilidad" 
                aria-expanded="false" 
                aria-controls="access-menu">
            <i class="bi bi-universal-access-circle fs-2"></i>
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>

</body>
</html>