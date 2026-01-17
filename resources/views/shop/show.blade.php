<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $producto->PRO_NOMBRE }} - Cap & Knit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    
    <style>
        /* --- ESTILOS BASE (MODO NORMAL) --- */
        body { background-color: #ffffff; color: #000000; }
        .u-bg-gray { background-color: #f3f4f6; border-color: #000000 !important; }
        
        /* Ajuste Dropdown */
        .dropdown-menu { z-index: 1050; }

        /* --- MODO ALTO CONTRASTE --- */
        body.high-contrast { background-color: #000000 !important; color: #ffff00 !important; }
        
        /* Contenedores */
        body.high-contrast .u-bg-gray, 
        body.high-contrast .card, 
        body.high-contrast .bg-light,
        body.high-contrast .alert { 
            background-color: #000000 !important; 
            border-color: #ffff00 !important; 
            color: #ffff00 !important; 
        }

        /* Textos */
        body.high-contrast a, body.high-contrast i, body.high-contrast h1, 
        body.high-contrast p, body.high-contrast span, body.high-contrast div { 
            color: #ffff00 !important; 
        }

        /* Botones */
        body.high-contrast .btn-dark {
            background-color: #ffff00 !important; 
            color: #000000 !important; 
            border-color: #ffff00 !important;
        }
        body.high-contrast .btn-outline-dark {
            color: #ffff00 !important; 
            border-color: #ffff00 !important;
        }

        /* Menú */
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
                    <li><a href="{{ route('shop.products') }}" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1">Productos</a></li>
                    
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

    <main id="main-content" class="py-5">
        <div class="container">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border border-success border-2 shadow-sm fw-bold mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('shop.products') }}" class="text-dark fw-bold text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Volver al catálogo
                </a>
            </div>

            <div class="row gx-5 align-items-center">
                
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card border-2 border-dark shadow">
                        <img src="{{ asset('img/productos/gorranewera.jpg') }}" 
                             class="card-img-top" 
                             alt="{{ $producto->PRO_NOMBRE }}"
                             style="width: 100%; height: auto; object-fit: cover;"
                             onerror="this.src='{{ asset('static/img/gorra_default.jpg') }}'">
                    </div>
                </div>

                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-3">{{ $producto->PRO_NOMBRE }}</h1>
                    
                    <div class="fs-2 fw-bold mb-3 text-success">
                        ${{ number_format($producto->PRO_PRECIO, 2) }}
                    </div>

                    <p class="lead text-muted mb-4 fs-5">
                        {{ $producto->PRO_DESCRIPCION }}
                    </p>

                    <hr class="border-dark opacity-100 my-4">

                    <form action="{{ route('add.to.cart', $producto->PRO_ID) }}" method="GET"> 
                        <div class="d-flex gap-3 align-items-center">
                            @auth
                                <button type="submit" class="btn btn-dark btn-lg px-5 fw-bold w-100 shadow border-2">
                                    <i class="bi bi-cart-plus me-2"></i> Añadir a la Cesta
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-5 fw-bold w-100 border-2">
                                    <i class="bi bi-lock-fill me-2"></i> Inicia sesión para comprar
                                </a>
                            @endauth
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="u-bg-gray border-top border-2 border-dark py-5 mt-5">
        <div class="container text-center text-md-start">
            <p class="mb-0 fw-bold">&copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>
</body>
</html>