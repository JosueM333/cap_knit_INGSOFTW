<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cap & Knit - Sistema de Gestión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logo-text { font-weight: bold; letter-spacing: 1px; }
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
        .nav-link.active { font-weight: bold; color: #000 !important; }
        .card-menu { transition: transform 0.2s; cursor: pointer; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    </style>
</head>

<body class="bg-light">

<header class="bg-white border-bottom sticky-top py-3 shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="text-dark text-decoration-none fw-bold fs-4">
            <span class="logo-text">CAP & KNIT</span>
        </a>

        <nav aria-label="Navegación principal">
            <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center">
                <li>
                    <a href="{{ route('home') }}" 
                       class="text-decoration-none {{ request()->routeIs('home') ? 'nav-link active' : 'text-secondary' }}">
                       Menú Principal
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('clientes.index') }}" 
                       class="text-decoration-none {{ request()->routeIs('clientes.*') ? 'nav-link active' : 'text-secondary' }}">
                       Clientes
                    </a>
                </li>
                <li>
                    <a href="{{ route('proveedores.index') }}" 
                       class="text-decoration-none {{ request()->routeIs('proveedores.*') ? 'nav-link active' : 'text-secondary' }}">
                       Proveedores
                    </a>
                </li>
                <li>
                    <a href="{{ route('bodegas.index') }}" 
                       class="text-decoration-none {{ request()->routeIs('bodegas.*') ? 'nav-link active' : 'text-secondary' }}">
                       Bodega
                    </a>
                </li>
                <li>
                    <a href="{{ route('productos.index') }}" 
                       class="text-decoration-none {{ request()->routeIs('productos.*') ? 'nav-link active' : 'text-secondary' }}">
                       Productos
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<main class="container py-4 flex-grow-1">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

<footer class="bg-white border-top py-4 mt-auto">
    <div class="container text-center">
        <p class="mb-0 text-muted small">&copy; {{ date('Y') }} Cap & Knit. Sistema de Gestión Administrativa.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>