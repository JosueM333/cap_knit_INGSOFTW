<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Cap & Knit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    
    <style>
        /* --- ESTILOS BASE (MODO NORMAL) --- */
        body {
            background-color: #ffffff;
            color: #000000;
        }

        /* Clase personalizada para Header y Footer en modo normal */
        .u-bg-gray {
            background-color: #f3f4f6; /* El gris suave que querías */
            border-color: #000000 !important; /* Bordes negros */
        }

        .u-text-dark {
            color: #000000 !important;
        }

        /* --- ESTILOS MODO ALTO CONTRASTE (Accessibility) --- */
        /* Cuando el body tiene la clase .high-contrast, todo cambia */
        
        body.high-contrast {
            background-color: #000000 !important;
            color: #ffff00 !important; /* Texto amarillo para máximo contraste */
        }

        /* Header y Footer en Alto Contraste */
        body.high-contrast .u-bg-gray {
            background-color: #000000 !important; /* Se vuelve negro */
            border-color: #ffff00 !important; /* Bordes amarillos */
            border-bottom: 2px solid #ffff00 !important;
        }

        /* Enlaces y Textos en Alto Contraste */
        body.high-contrast a, 
        body.high-contrast i,
        body.high-contrast .logo-text,
        body.high-contrast .contrast-text,
        body.high-contrast h1, 
        body.high-contrast p {
            color: #ffff00 !important; /* Todo amarillo */
        }
        
        body.high-contrast .btn-outline-dark {
            border-color: #ffff00 !important;
            color: #ffff00 !important;
        }
        
        body.high-contrast .btn-dark {
            background-color: #ffff00 !important;
            color: #000000 !important; /* Texto negro sobre botón amarillo */
            border-color: #ffff00 !important;
        }

        body.high-contrast .dropdown-menu {
            background-color: #000000 !important;
            border: 2px solid #ffff00 !important;
        }
        
        body.high-contrast .dropdown-item {
            color: #ffff00 !important;
        }
        body.high-contrast .dropdown-item:hover {
            background-color: #333 !important;
        }
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
                <li><a href="{{ route('shop.products') }}" class="text-dark text-decoration-none border-bottom border-dark border-2 pb-1">Productos</a></li>
                
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
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            @if(Auth::user()->CLI_EMAIL == 'admin@gmail.com') 
                                <li>
                                    <a href="{{ route('home') }}" class="dropdown-item fw-bold text-primary">
                                        <i class="bi bi-gear-fill me-2"></i> Panel Admin
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider border-dark"></li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold">
                                        <i class="bi bi-box-arrow-right me-2"></i> Salir
                                    </button>
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
    <section class="min-vh-100 d-flex align-items-center justify-content-center hero-section"
             style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('img/background.jpg') }}') center/cover;">

        <div class="sr-only">
            Persona usando una gorra de lana tejida a mano en un día de invierno
        </div>

        <div class="container text-center text-white">
            <h1 class="display-2 fw-light text-uppercase mb-4 fw-bold text-light">
                La calidez se encuentra con el estilo
            </h1>
            <p class="fs-4 mb-5 text-light fw-bold">Gorras y gorros tejidos con materiales de la más alta calidad</p>
            
            <a href="{{ route('shop.products') }}" class="btn btn-light btn-lg px-5 text-uppercase fw-bold border-2 border-dark shadow">
                Ver productos
                <span class="sr-only"> de Cap & Knit</span>
            </a>
        </div>
    </section>
</main>

<footer class="u-bg-gray border-top border-2 py-5" role="contentinfo">
    <div class="container text-center text-md-start">
        <p class="mb-0 fw-bold">&copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.</p>
        <p class="small text-muted mt-2 contrast-text">
            <a href="#main-content" class="text-dark fw-bold text-decoration-none">Volver arriba ↑</a>
        </p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('accessibility.js') }}"></script>

</body>
</html>