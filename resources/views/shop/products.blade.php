<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colección - Cap & Knit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>

<body>

    <header class="sticky-top py-3 shadow-sm border-bottom border-secondary" role="banner">
        <div class="container d-flex justify-content-between align-items-center">

            <a href="{{ route('shop.index') }}"
                class="text-decoration-none fw-bold fs-4 d-flex align-items-center gap-2 text-white">
                <span class="logo-text">CAP & KNIT</span>
            </a>

            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled mb-0 fw-bold align-items-center text-uppercase small">
                    <li>
                        <a href="{{ route('shop.products') }}"
                            class="text-white text-decoration-none border-bottom border-2 border-white pb-1"
                            aria-current="page">
                            Productos
                        </a>
                    </li>

                    @guest('cliente')
                        <li>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-bold">
                                <i class="bi bi-person" aria-hidden="true"></i> Login
                            </a>
                        </li>
                    @endguest

                    @auth('cliente')
                        <li class="dropdown">
                            <a href="#" class="btn btn-outline-light btn-sm fw-bold dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-fill"></i> Hola, {{ Auth::guard('cliente')->user()->CLI_NOMBRES }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                @if(Auth::guard('cliente')->user()->CLI_EMAIL === 'admin@gmail.com')
                                    <li>
                                        <a href="{{ route('home') }}" class="dropdown-item fw-bold text-primary">
                                            <i class="bi bi-gear-fill me-2"></i> Panel Admin
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
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
                    @endauth

                    <li>
                        <a href="{{ route('shop.cart') }}" class="text-white text-decoration-none"
                            aria-label="Carrito de compras, {{ count(session('cart', [])) }} artículos"
                            data-bs-toggle="tooltip" title="Ver Carrito">
                            <i class="bi bi-cart-fill fs-5" aria-hidden="true"></i>
                            <span class="d-none d-md-inline ms-1">Carrito</span>
                            ({{ count(session('cart', [])) }})
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" class="container py-5 my-4" tabindex="-1">

        <div class="text-center mb-5 animate-entry">
            <h1 class="display-4 fw-bold text-uppercase mb-3">Nuestra Colección</h1>
            <p class="fs-5 text-muted" style="max-width: 600px; margin: 0 auto;">
                Calidad artesanal y estilo moderno para mantenerte abrigado esta temporada.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($productos as $loop => $producto)
                <div class="col-10 col-md-6 col-lg-3 animate-entry" style="animation-delay: {{ $loop->iteration * 0.1 }}s">

                    <article class="card h-100 shadow-sm border-0 d-flex flex-column">
                        <div class="position-relative overflow-hidden bg-white" style="height: 250px;">
                            {{-- LÓGICA DE IMAGEN DINÁMICA: Busca img/productos/{CODIGO}.jpg --}}
                            <img src="{{ asset('img/productos/' . $producto->PRO_CODIGO . '.jpg') }}"
                                class="w-100 h-100 object-fit-cover" alt="Vista del producto {{ $producto->PRO_NOMBRE }}"
                                onerror="this.onerror=null; this.src='{{ asset('img/productos/gorranewera.jpg') }}';">
                        </div>

                        <div class="card-body text-center d-flex flex-column p-4">
                            <h2 class="h5 card-title fw-bold mb-2">{{ $producto->PRO_NOMBRE }}</h2>

                            <p class="fw-bold mb-4 fs-5 text-primary">
                                <span class="visually-hidden">Precio:</span>
                                ${{ number_format($producto->PRO_PRECIO, 2) }}
                            </p>

                            <div class="mt-auto d-grid gap-2">
                                <a href="{{ route('add.to.cart', $producto->PRO_ID) }}" class="btn btn-outline-dark fw-bold"
                                    aria-label="Añadir {{ $producto->PRO_NOMBRE }} al carrito" data-bs-toggle="tooltip"
                                    title="Añadir al carrito">
                                    <i class="bi bi-cart-plus me-1"></i> Añadir
                                </a>

                                <a href="{{ route('shop.show', $producto->PRO_ID) }}" class="btn btn-dark fw-bold"
                                    aria-label="Ver detalles de {{ $producto->PRO_NOMBRE }}" data-bs-toggle="tooltip"
                                    title="Ver detalles del producto">
                                    Ver Detalle
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 animate-entry">
                    <div class="alert alert-light border border-secondary text-center fw-bold py-5" role="alert">
                        <i class="bi bi-info-circle fs-1 d-block mb-3 text-muted"></i>
                        <h3 class="h5">No hay productos disponibles por el momento.</h3>
                        <p class="mb-0">Vuelve a visitarnos pronto.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="py-3 mt-auto" role="contentinfo">
        <div class="container text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-0 fw-bold text-white">&copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#main-content"
                        class="text-white text-decoration-none fw-bold small opacity-75 hover-opacity-100">
                        Volver arriba <i class="bi bi-arrow-up-short"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <div class="accessibility-widget">
        <div id="access-menu" class="access-menu p-3" hidden>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h2 class="h6 fw-bold mb-0 text-dark">Accesibilidad</h2>
                <button type="button" class="btn-close" aria-label="Cerrar menú"
                    onclick="document.getElementById('access-btn').click()"></button>
            </div>

            <div class="mb-3">
                <span class="d-block small fw-bold mb-2 text-dark">Tamaño de texto</span>
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-decrease-font"
                        aria-label="Disminuir">A-</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-reset-font"
                        aria-label="Normal">A</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-increase-font"
                        aria-label="Aumentar">A+</button>
                </div>
            </div>

            <div class="mb-3">
                <button id="contrast-toggle-widget"
                    class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Alto Contraste</span>
                    <i class="bi bi-circle-half"></i>
                </button>
            </div>

            <div class="mb-1">
                <button id="links-toggle"
                    class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Subrayar Enlaces</span>
                    <i class="bi bi-type-underline"></i>
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
    <script>
        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
</body>

</html>