<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Cap & Knit | Gorras y Gorros de Lana Premium</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        /* --- ESTILOS BASE (MODO NORMAL) --- */
        body { background-color: #ffffff; color: #000000; }
        .u-bg-gray { background-color: #f3f4f6; border-color: #000000 !important; }
        
        /* Ajustes visuales extra */
        .dropdown-menu { z-index: 1050; }
        .transition-hover { transition: transform 0.2s; }
        .transition-hover:hover { transform: translateY(-5px); }

        /* --- MODO ALTO CONTRASTE (AA) --- */
        body.high-contrast { background-color: #000000 !important; color: #ffff00 !important; }
        
        /* Contenedores principales */
        body.high-contrast .u-bg-gray, 
        body.high-contrast .card,
        body.high-contrast .bg-white,
        body.high-contrast .alert { 
            background-color: #000000 !important; 
            border-color: #ffff00 !important; 
            color: #ffff00 !important; 
        }

        /* Textos e Iconos */
        body.high-contrast a, body.high-contrast i, body.high-contrast h1, body.high-contrast h2, 
        body.high-contrast h3, body.high-contrast h4, body.high-contrast h5, body.high-contrast p, 
        body.high-contrast span { 
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

        /* Menús Desplegables */
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
                <i class="bi bi-shop"></i>
                <span class="logo-text">CAP & KNIT</span>
            </a>

            <button id="contrast-toggle" class="btn btn-sm btn-outline-dark fw-bold border-2"
                    aria-pressed="false"
                    aria-label="Activar modo de alto contraste">
                <i class="bi bi-circle-half me-1"></i>
                <span class="contrast-text">Alto contraste</span>
            </button>

            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center fw-bold">
                    <li>
                        <a href="{{ route('shop.products') }}" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1">Productos</a>
                    </li>
                    
                    @guest
                        <li>
                            <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm fw-bold">
                                <i class="bi bi-person"></i> Iniciar sesión
                            </a>
                        </li>
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
                        <a href="{{ route('shop.cart') }}" class="text-dark text-decoration-none">
                            <i class="bi bi-cart-fill fs-5"></i> Cesta (<span id="cart-count">0</span>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        <section class="container py-5 my-5">
            <h1 id="productos-titulo" class="display-4 text-center text-uppercase fw-light pb-4 mb-5">
                Gorras y Gorros de Lana
            </h1>

            <div class="row g-4 justify-content-center">
                @forelse($productos as $producto)
                    <div class="col-10 col-md-4 col-lg-3">
                        <div class="card h-100 border-dark border-1 shadow transition-hover">
                            <img src="{{ asset('img/productos/gorranewera.jpg') }}" 
                                class="card-img-top border-bottom border-dark" 
                                style="height:200px; object-fit:cover;" 
                                alt="Imagen de {{ $producto->PRO_NOMBRE }}"
                                onerror="this.src='{{ asset('static/img/gorra_default.jpg') }}'">

                            <div class="card-body text-center d-flex flex-column bg-white">
                                <h5 class="card-title fw-bold">{{ $producto->PRO_NOMBRE }}</h5>
                                <p class="text-dark fw-bold mb-2 fs-5">${{ number_format($producto->PRO_PRECIO, 2) }}</p>
                                <p class="card-text small text-muted text-truncate">{{ $producto->PRO_DESCRIPCION ?? 'Descripción no disponible' }}</p>
                                
                                <div class="mt-auto">
                                    <a href="{{ route('shop.show', $producto->PRO_ID) }}" class="btn btn-dark w-100 stretched-link fw-bold">Ver Detalle</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border border-dark text-center py-5 shadow-sm" role="alert">
                            <h4 class="alert-heading fw-bold"><i class="bi bi-info-circle"></i> Catálogo vacío</h4>
                            <p>No hay productos disponibles en este momento.</p>
                            @auth
                                <hr class="border-dark">
                                <p class="mb-0">Admin: Ve al <a href="{{ route('productos.create') }}" class="fw-bold text-dark">Panel</a> para añadir stock.</p>
                            @endauth
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="u-bg-gray border-top border-2 py-5" role="contentinfo">
        <div class="container text-center text-md-start">
            <p class="mb-0 fw-bold">&copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.</p>
            <p class="small text-muted mt-2">
                <a href="#main-content" class="text-dark fw-bold text-decoration-none">Volver arriba ↑</a>
            </p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>

</body>
</html>