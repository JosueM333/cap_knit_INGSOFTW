<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Cap & Knit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <style>
        .transition-hover { transition: transform 0.2s; }
        .transition-hover:hover { transform: translateY(-5px); }
        /* Tus estilos de alto contraste irían aquí... */
    </style>
</head>
<body>

<header class="u-bg-gray border-bottom border-2 sticky-top py-3 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('shop.index') }}" class="text-dark text-decoration-none fw-bold fs-4">
            <i class="bi bi-shop"></i> CAP & KNIT
        </a>
        <nav>
            <ul class="d-flex gap-4 list-unstyled mb-0 fw-bold align-items-center">
                <li><a href="{{ route('shop.products') }}" class="text-dark text-decoration-none border-bottom border-dark">Productos</a></li>
                <li><a href="{{ route('shop.cart') }}" class="text-dark text-decoration-none"><i class="bi bi-cart-fill"></i> Carrito ({{ count(session('cart', [])) }})</a></li>
                @guest
                    <li><a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark fw-bold">Login</a></li>
                @else
                    <li><a href="{{ route('home') }}" class="btn btn-sm btn-dark fw-bold">Admin</a></li>
                @endguest
            </ul>
        </nav>
    </div>
</header>

<main class="container py-5 my-5">
    <h1 class="display-4 text-center text-uppercase fw-light pb-4 mb-5">Gorras y Gorros</h1>
    <div class="row g-4 justify-content-center">
        @forelse($productos as $producto)
            <div class="col-10 col-md-4 col-lg-3">
                <div class="card h-100 border-dark border-1 shadow transition-hover">
                    <img src="{{ asset('img/productos/gorranewera.jpg') }}" 
                         class="card-img-top border-bottom border-dark" 
                         style="height:200px; object-fit:cover;" 
                         alt="{{ $producto->PRO_NOMBRE }}"
                         onerror="this.src='{{ asset('static/img/gorra_default.jpg') }}'">

                    <div class="card-body text-center d-flex flex-column bg-white">
                        <h5 class="card-title fw-bold">{{ $producto->PRO_NOMBRE }}</h5>
                        <p class="text-dark fw-bold mb-2 fs-5">${{ number_format($producto->PRO_PRECIO, 2) }}</p>
                        
                        <div class="mt-auto d-grid gap-2">
                            {{-- BOTÓN RÁPIDO PARA AÑADIR --}}
                            <a href="{{ route('add.to.cart', $producto->PRO_ID) }}" class="btn btn-outline-dark fw-bold">
                                <i class="bi bi-cart-plus"></i> Añadir
                            </a>
                            <a href="{{ route('shop.show', $producto->PRO_ID) }}" class="btn btn-dark fw-bold">Ver Detalle</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-light border border-dark text-center">No hay productos.</div></div>
        @endforelse
    </div>
</main>

<footer class="u-bg-gray border-top py-5"><div class="container text-center fw-bold">&copy; {{ date('Y') }} Cap & Knit.</div></footer>
</body>
</html>