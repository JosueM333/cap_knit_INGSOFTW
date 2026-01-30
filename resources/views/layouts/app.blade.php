<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión - Cap & Knit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body class="bg-light">

    <header class="navbar-custom sticky-top py-3 shadow-sm mb-4">
        <div class="container d-flex justify-content-between align-items-center">

            <a href="{{ url('/') }}"
                class="text-white text-decoration-none fw-bold fs-4 d-flex align-items-center gap-2">
                <span style="font-family: var(--font-heading); letter-spacing: 1px;">CAP & KNIT</span>
            </a>

            <nav class="d-none d-lg-block">
                <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center">
                    <li><a href="{{ route('home') }}" class="text-decoration-none nav-link-custom">Admin</a></li>
                    <li><a href="{{ route('shop.index') }}" class="text-decoration-none nav-link-custom">Tienda</a></li>
                    @auth('cliente')
                        <li><a href="{{ route('shop.pending') }}" class="text-decoration-none nav-link-custom">Pedidos
                                Pendientes</a></li>
                    @endauth
                    <li>
                        <a href="{{ route('shop.cart') }}" class="text-decoration-none nav-link-custom"
                            aria-label="Ver carrito de compras">
                            <i class="bi bi-cart-fill fs-5 me-1"></i> Carrito (<span
                                id="cart-count">{{ count(session('cart', [])) }}</span>)
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="d-flex align-items-center gap-3">
                {{-- Botón de Ayuda (Heurística #10) --}}
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" data-bs-toggle="modal"
                    data-bs-target="#helpModal" title="Ayuda">
                    <i class="bi bi-question-lg"></i>
                </button>

                @if(Auth::check() || Auth::guard('cliente')->check())
                    <span class="fw-bold text-success">
                        <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                        <span class="d-none d-md-inline">Conectado</span>
                    </span>

                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 fw-bold"
                            aria-label="Cerrar sesión">
                            Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-dark fw-bold">Iniciar Sesión</a>
                @endif
            </div>

        </div>
    </header>

    {{-- Modal de Ayuda (Heurística #10) --}}
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="helpModalLabel"><i class="bi bi-info-circle text-primary"></i> Ayuda del
                        Sistema</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Bienvenido al Sistema de Gestión Cap & Knit</h6>
                    <p class="small text-muted">A continuación se describen las funciones principales:</p>
                    <ul class="small">
                        <li><strong>Navegación:</strong> Use el menú superior para acceder a las diferentes secciones
                            (Tienda, Admin).</li>
                        <li><strong>Búsquedas:</strong> Utilice las barras de búsqueda para encontrar registros
                            específicos rápidamente.</li>
                        <li><strong>Acciones:</strong> Los botones de editar y eliminar le permiten gestionar la
                            información. El sistema le pedirá confirmación para acciones críticas.</li>
                    </ul>
                    <hr>
                    <p class="mb-0 small text-muted">Si necesita soporte técnico, contacte al administrador del sistema.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <main class="container py-4 flex-grow-1">
        {{-- Alertas Globales --}}
        @if(session('success'))
            <div class="alert alert-success border-success fw-bold" role="alert">
                <i class="bi bi-check-circle me-2" aria-hidden="true"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-danger fw-bold" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i> {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Modal Global de Confirmación de Eliminación --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteMessage">¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede
                        deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" action="" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-primary-dark text-white border-top border-secondary py-5 mt-auto">
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
                    <a href="#" class="text-accent fw-bold text-decoration-none small">
                        Volver arriba <i class="bi bi-arrow-up"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Global: Deshabilitar botón submit y mostrar spinner
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (this.classList.contains('submitting')) {
                        e.preventDefault();
                        return;
                    }
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        if (!this.checkValidity()) {
                            return;
                        }
                        this.classList.add('submitting');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
                    }
                });
            });

            // Script para manejar el Modal de Eliminación dinámicamente
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const actionUrl = button.getAttribute('data-action');
                    const itemName = button.getAttribute('data-item-name');

                    const form = deleteModal.querySelector('#deleteForm');
                    const message = deleteModal.querySelector('#deleteMessage');

                    if (form && actionUrl) form.action = actionUrl;

                    if (message) {
                        if (itemName) {
                            message.textContent = `¿Estás seguro de que deseas eliminar "${itemName}"? Esta acción no se puede deshacer.`;
                        } else {
                            message.textContent = "¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.";
                        }
                    }
                });
            }

            // Global Hotkeys (Heurística #7)
            document.addEventListener('keydown', function (e) {
                // F2: Focus Search
                if (e.key === 'F2') {
                    // Try to find a search input
                    const searchInput = document.querySelector('input[name="search"]') || document.querySelector('input[name="criterio"]');
                    if (searchInput) {
                        e.preventDefault();
                        searchInput.focus();
                    }
                }
            });
        });
    </script>

</body>

</html>