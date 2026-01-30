<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Cap & Knit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>

<body>

    <a href="#main-content" class="skip-link">
        Saltar al contenido principal
    </a>

    <header class="navbar-custom border-bottom border-secondary sticky-top py-3 shadow-sm" role="banner">
        <div class="container d-flex justify-content-between align-items-center">

            <a href="{{ route('shop.index') }}"
                class="text-white text-decoration-none fw-bold fs-4 d-flex align-items-center">
                <span class="logo-text">CAP & KNIT</span>
            </a>

            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center fw-bold">
                    <li>
                        <a href="{{ route('shop.products') }}"
                            class="text-white text-decoration-none border-bottom border-white border-2 pb-1">
                            Productos
                        </a>
                    </li>

                    {{-- 1. SI ES ADMINISTRADOR --}}
                    @auth('web')
                        <li class="dropdown">
                            <a href="#" class="btn btn-outline-light btn-sm fw-bold dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-badge-fill" aria-hidden="true"></i> Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a href="{{ route('home') }}" class="dropdown-item fw-bold text-primary">
                                        <i class="bi bi-speedometer2 me-2"></i> Panel Admin
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
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
                    @endauth

                    {{-- 2. SI ES CLIENTE --}}
                    @auth('cliente')
                        <li>
                            <a href="{{ route('shop.pending') }}"
                                class="text-white text-decoration-none border-bottom border-white border-2 pb-1">
                                Pedidos Pendientes
                            </a>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="btn btn-outline-light btn-sm fw-bold dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-fill" aria-hidden="true"></i>
                                Hola, {{ Auth::guard('cliente')->user()->CLI_NOMBRES }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                @if(Auth::guard('cliente')->user()->CLI_EMAIL === 'admin@gmail.com')
                                    <li>
                                        <a href="{{ route('home') }}" class="dropdown-item fw-bold text-primary">
                                            <i class="bi bi-gear-fill me-2" aria-hidden="true"></i> Panel Admin
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider border-dark">
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('shop.pending') }}" class="dropdown-item fw-bold">
                                        <i class="bi bi-clock-history me-2"></i> Pedidos Pendientes
                                    </a>
                                </li>

                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger fw-bold">
                                            <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i> Salir
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth

                    {{-- 3. SI NO HAY NADIE CONECTADO (NI WEB NI CLIENTE) --}}
                    @if(!Auth::guard('web')->check() && !Auth::guard('cliente')->check())
                        <li>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold">
                                <i class="bi bi-person" aria-hidden="true"></i> Iniciar sesión
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('shop.cart') }}" class="text-white text-decoration-none"
                            aria-label="Ver carrito de compras">
                            <i class="bi bi-cart-fill fs-5" aria-hidden="true"></i>
                            Carrito (<span id="cart-count">{{ count(session('cart', [])) }}</span>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        {{-- HERO SECTION --}}
        <section class="min-vh-100 d-flex align-items-center justify-content-center hero-section position-relative"
            style="background: url('{{ asset('img/hero_cats.png') }}') center/cover; margin-top: -72px;">
            <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>

            <div class="container text-center text-white position-relative" style="z-index: 2;">
                <span class="text-uppercase fw-bold text-accent letter-spacing-2 mb-3 d-block"></span>
                <h1 class="hero-title fw-bold text-light mb-4 text-uppercase">
                    Eleva tu Estilo
                </h1>
                <p class="fs-4 mb-5 text-light opacity-90 fw-light" style="max-width: 700px; margin: 0 auto;">
                    Fusionamos la frescura del streetwear con la calidez del tejido artesanal. Descubre nuestra nueva
                    colección de temporada.
                </p>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('shop.products') }}" class="btn btn-premium fs-5 px-5 py-3 border-0">
                        Explorar Colección
                    </a>
                </div>
            </div>
        </section>

        {{-- FEATURED CATEGORIES --}}
        <section class="py-5 bg-white">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="display-5 text-uppercase mb-2">Nuestras Colecciones</h2>
                    <p class="text-muted">Diseñado para destacar en cualquier ambiente.</p>
                </div>

                <div class="row g-4 justify-content-center">
                    {{-- Featured Collection: Accessories (Neko Arc) --}}
                    <div class="col-md-8">
                        <a href="{{ route('shop.products', ['category' => 'accessories']) }}" class="text-white">
                            <div class="category-card shadow" style="height: 1000x;">
                                <img src="{{ asset('img/cat_accessories.png') }}" alt="Accesorios">
                                <div class="category-overlay">
                                    <h3 class="h3 fw-bold mb-1 text-white">Colección Exclusiva: Accesorios</h3>
                                    <span class="text-uppercase fw-bold text-accent">Descubrir Ahora &rarr;</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- VALUE PROPOSITIONS --}}
        <section class="py-5 u-bg-gray">
            <div class="container py-5">
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="p-4">
                            <i class="bi bi-patch-check-fill display-4 text-accent mb-3"></i>
                            <h4 class="h5 fw-bold text-uppercase">Calidad Garantizada</h4>
                            <p class="text-muted small">Materiales seleccionados meticulosamente para durar.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4">
                            <i class="bi bi-truck display-4 text-accent mb-3"></i>
                            <h4 class="h5 fw-bold text-uppercase">Envío Rápido</h4>
                            <p class="text-muted small">Recibe tus productos en tiempo récord.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4">
                            <i class="bi bi-shield-lock-fill display-4 text-accent mb-3"></i>
                            <h4 class="h5 fw-bold text-uppercase">Compra Segura</h4>
                            <p class="text-muted small">Tus datos están protegidos con la mejor tecnología.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-primary-dark text-white border-top border-secondary py-5" role="contentinfo">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold text-uppercase mb-3">CAP & KNIT</h5>
                    <p class="small text-secondary">
                        Redefiniendo el estilo urbano con la tradición del tejido. Calidad, confort y diseño en cada
                        pieza.
                    </p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold text-uppercase mb-3">Enlaces Rápidos</h5>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('shop.index') }}" class="text-secondary text-decoration-none">Tienda</a>
                        </li>
                        <li><a href="{{ route('shop.products') }}"
                                class="text-secondary text-decoration-none">Productos</a></li>
                        <li><a href="{{ route('login') }}" class="text-secondary text-decoration-none">Mi Cuenta</a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold text-uppercase mb-3">Contacto</h5>
                    <p class="small text-secondary mb-1"><i class="bi bi-geo-alt me-2"></i> Calle Principal 123, Ciudad
                    </p>
                    <p class="small text-secondary"><i class="bi bi-envelope me-2"></i> hola@capandknit.com</p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 fw-bold small text-secondary">
                        &copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#main-content" class="text-accent fw-bold text-decoration-none small">
                        Volver arriba <i class="bi bi-arrow-up"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <div class="accessibility-widget">
        <div id="access-menu" class="access-menu p-3" hidden>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h2 class="h6 fw-bold mb-0">Accesibilidad</h2>
                <button type="button" class="btn-close" aria-label="Cerrar menú"
                    onclick="document.getElementById('access-btn').click()"></button>
            </div>

            <div class="mb-3">
                <span class="d-block small fw-bold mb-2">Tamaño de texto</span>
                <div class="btn-group w-100" role="group" aria-label="Controles de tamaño de texto">
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-decrease-font"
                        aria-label="Disminuir letra">A-</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-reset-font"
                        aria-label="Letra normal">A</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-increase-font"
                        aria-label="Aumentar letra">A+</button>
                </div>
            </div>

            <div class="mb-3">
                <button id="contrast-toggle-widget"
                    class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Alto Contraste</span>
                    <i class="bi bi-circle-half" aria-hidden="true"></i>
                </button>
            </div>

            <div class="mb-1">
                <button id="links-toggle"
                    class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Subrayar Enlaces</span>
                    <i class="bi bi-type-underline" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <button id="access-btn" class="btn btn-dark rounded-circle shadow-lg p-3 mt-2"
            aria-label="Abrir menú de accesibilidad" aria-expanded="false" aria-controls="access-menu">
            <i class="bi bi-universal-access-circle fs-2"></i>
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>

</body>

</html>