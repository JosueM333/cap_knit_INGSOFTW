<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda - Cap & Knit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        /* Asegurar que el footer se quede abajo si hay poco contenido */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
        }

        /* Fix: Forzar color negro en items del dropdown para evitar que se vean blancos sobre blanco */
        .dropdown-item {
            color: #212529 !important;
        }

        .dropdown-item:hover {
            background-color: #e9ecef !important;
            color: #1a1e21 !important;
        }
    </style>
    @yield('styles')
</head>

<body>

    <header class="u-bg-gray border-bottom border-2 py-3 sticky-top shadow-sm" role="banner">
        <div class="container d-flex justify-content-between align-items-center">

            <a href="{{ route('shop.index') }}"
                class="text-white text-decoration-none fw-bold fs-4 d-flex align-items-center">
                <span class="logo-text text-white">CAP & KNIT</span>
            </a>

            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center fw-bold">
                    <li>
                        <a href="{{ route('shop.products') }}"
                            class="text-white text-decoration-none border-bottom border-light border-2 pb-1">
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
                        <li class="dropdown">
                            <a href="#" class="btn btn-outline-light btn-sm fw-bold dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-fill" aria-hidden="true"></i>
                                Hola, {{ Auth::guard('cliente')->user()->CLI_NOMBRES }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <a href="{{ route('shop.purchases') }}" class="dropdown-item fw-bold">
                                        <i class="bi bi-bag-check-fill me-2" aria-hidden="true"></i> Mis Compras
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider border-dark">
                                </li>
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
                            aria-label="Ver carrito de compras" data-bs-toggle="tooltip" title="Ver Carrito">
                            <i class="bi bi-cart-fill fs-5" aria-hidden="true"></i>
                            Carrito (<span id="cart-count">{{ count(session('cart', [])) }}</span>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        @yield('content')
    </main>

    <footer class="u-bg-gray border-top border-2 py-5 mt-auto" role="contentinfo">
        <div class="container text-center text-md-start">
            <p class="mb-0 fw-bold text-white">
                &copy; {{ date('Y') }} Cap & Knit. Todos los derechos reservados.
            </p>
            <p class="small text-muted mt-2 contrast-text">
                <a href="#main-content" class="text-white fw-bold text-decoration-none">
                    Volver arriba ↑
                </a>
            </p>
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
    <script>
        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
    @yield('scripts')
</body>

</html>