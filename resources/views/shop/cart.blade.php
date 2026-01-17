<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cesta de Compra - Cap & Knit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    
    <style>
        /* --- ESTILOS BASE (MODO NORMAL) --- */
        body { background-color: #ffffff; color: #000000; }
        .u-bg-gray { background-color: #f3f4f6; border-color: #000000 !important; }

        /* --- MODO ALTO CONTRASTE (AA) --- */
        body.high-contrast { background-color: #000000 !important; color: #ffff00 !important; }
        
        body.high-contrast .u-bg-gray, 
        body.high-contrast .bg-white,
        body.high-contrast .alert,
        body.high-contrast .table,
        body.high-contrast .table-light { 
            background-color: #000000 !important; 
            border-color: #ffff00 !important; 
            color: #ffff00 !important; 
        }

        body.high-contrast a, body.high-contrast i, body.high-contrast h1, body.high-contrast h2, 
        body.high-contrast h3, body.high-contrast h5, body.high-contrast p, body.high-contrast span,
        body.high-contrast th, body.high-contrast td { 
            color: #ffff00 !important; 
        }
        
        /* Corregir inputs en tabla */
        body.high-contrast input {
            background-color: #000 !important;
            color: #ff0 !important;
            border-color: #ff0 !important;
        }

        body.high-contrast .btn-dark, body.high-contrast .btn-success {
            background-color: #ffff00 !important; 
            color: #000000 !important; 
            border-color: #ffff00 !important;
        }
        body.high-contrast .btn-outline-dark, body.high-contrast .btn-outline-danger {
            color: #ffff00 !important; 
            border-color: #ffff00 !important;
        }
        
        body.high-contrast .dropdown-menu {
            background-color: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        body.high-contrast .dropdown-item { color: #ffff00 !important; }
        body.high-contrast .dropdown-item:hover { background-color: #333 !important; }
    </style>
</head>

<body>

    <header class="u-bg-gray border-bottom border-2 sticky-top py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            
            <a href="{{ route('shop.index') }}" class="text-dark text-decoration-none fw-bold fs-4 d-flex align-items-center gap-2">
                <i class="bi bi-shop"></i> <span class="logo-text">CAP & KNIT</span>
            </a>

            <button id="contrast-toggle" class="btn btn-sm btn-outline-dark fw-bold border-2" aria-pressed="false">
                <i class="bi bi-circle-half me-1"></i> Alto contraste
            </button>

            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center fw-bold">
                    <li>
                        <a href="{{ route('shop.products') }}" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1">Productos</a>
                    </li>
                    
                    @guest
                        <li><a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm fw-bold"><i class="bi bi-person"></i> Iniciar sesión</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="btn btn-dark btn-sm fw-bold dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-person-fill"></i> Hola, {{ Auth::user()->CLI_NOMBRES }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-2 border-dark">
                                @if(Auth::user()->CLI_EMAIL == 'admin@gmail.com') 
                                    <li><a href="{{ route('home') }}" class="dropdown-item fw-bold text-primary"><i class="bi bi-gear-fill me-2"></i> Panel Admin</a></li>
                                    <li><hr class="dropdown-divider border-dark"></li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest

                    <li>
                        <a href="{{ route('shop.cart') }}" class="text-dark text-decoration-none fw-bold">
                            <i class="bi bi-cart-fill fs-5"></i> Cesta (<span id="cart-count">{{ count(session('cart', [])) }}</span>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container my-5" tabindex="-1" id="main-content">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border border-success border-2 shadow-sm fw-bold" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <h1 class="mb-4 display-5 fw-bold text-uppercase">Tu Cesta de Compra</h1>

        @if(session('cart'))
            <div class="table-responsive">
                <table class="table table-hover border border-dark border-2 shadow">
                    <thead class="table-light border-bottom border-dark">
                        <tr>
                            <th style="width:50%" class="py-3">Producto</th>
                            <th style="width:10%" class="py-3">Precio</th>
                            <th style="width:8%" class="py-3">Cantidad</th>
                            <th style="width:22%" class="text-center py-3">Subtotal</th>
                            <th style="width:10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0 @endphp
                        @foreach(session('cart') as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <tr class="align-middle">
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 50px; height: 50px;">
                                            <img src="{{ asset('img/productos/gorranewera.jpg') }}" 
                                                 alt="{{ $details['name'] }}"
                                                 class="img-fluid border border-dark rounded"
                                                 style="width: 100%; height: 100%; object-fit: cover;"
                                                 onerror="this.src='{{ asset('static/img/gorra_default.jpg') }}'">
                                        </div>
                                        <div>
                                            <h5 class="mb-0 fw-bold">{{ $details['name'] }}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-bold">${{ number_format($details['price'], 2) }}</td>
                                <td>
                                    <input type="number" value="{{ $details['quantity'] }}" class="form-control form-control-sm text-center border-dark fw-bold" readonly>
                                </td>
                                <td class="text-center fw-bold text-success">
                                    ${{ number_format($details['price'] * $details['quantity'], 2) }}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('remove.from.cart') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="btn btn-outline-danger btn-sm border-2 fw-bold" title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light border-top border-dark">
                        <tr>
                            <td colspan="5" class="text-end p-4">
                                <h3 class="fw-bold mb-3">Total: ${{ number_format($total, 2) }}</h3>
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('shop.products') }}" class="btn btn-outline-dark fw-bold border-2">
                                        <i class="bi bi-arrow-left"></i> Seguir comprando
                                    </a>
                                    <button class="btn btn-success fw-bold border-2 shadow px-4">
                                        Pagar ahora <i class="bi bi-credit-card ms-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5 border border-dark border-2 rounded shadow-sm bg-light">
                <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                <h3 class="fw-bold">Tu cesta está vacía</h3>
                <p class="text-muted">¡No dejes que se escapen tus gorros favoritos!</p>
                <a href="{{ route('shop.products') }}" class="btn btn-dark mt-3 px-5 fw-bold shadow">Ver productos</a>
            </div>
        @endif
    </main>

    <footer class="u-bg-gray border-top border-2 py-5 mt-auto">
        <div class="container text-center text-md-start">
            <p class="mb-0 fw-bold">&copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.</p>
            <p class="small text-muted mt-2">
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>
</body>
</html>