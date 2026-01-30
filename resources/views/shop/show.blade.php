@extends('layouts.shop')

@section('content')
    <div class="container py-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border border-success border-2 shadow-sm fw-bold mb-4"
                role="alert">
                <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar notificación"></button>
            </div>
        @endif

        <nav aria-label="Navegación de ruta" class="mb-4">
            <a href="{{ route('shop.products') }}"
                class="text-dark fw-bold text-decoration-none d-inline-flex align-items-center">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
                Volver al catálogo
            </a>
        </nav>

        <div class="row gx-5 align-items-center">

            <div class="col-md-6 mb-4 mb-md-0">
                <div class="card border-2 border-dark shadow">
                    <img src="{{ asset('img/productos/' . $producto->PRO_CODIGO . '.jpg') }}" class="card-img-top"
                        alt="Vista detallada de {{ $producto->PRO_NOMBRE }}"
                        style="width: 100%; height: auto; object-fit: cover;"
                        onerror="this.onerror=null; this.src='{{ asset('img/productos/gorranewera.jpg') }}';">
                </div>
            </div>

            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-3">{{ $producto->PRO_NOMBRE }}</h1>

                <div class="fs-2 fw-bold mb-3 text-success">
                    <span class="visually-hidden">Precio: </span>
                    ${{ number_format($producto->PRO_PRECIO, 2) }}
                </div>

                <p class="lead text-muted mb-4 fs-5">
                    {{ $producto->PRO_DESCRIPCION }}
                </p>

                <hr class="border-dark opacity-100 my-4">

                <div class="d-flex gap-3 align-items-center">
                    @auth('cliente')
                        <a href="{{ route('add.to.cart', $producto->PRO_ID) }}"
                            class="btn btn-dark btn-lg px-5 fw-bold w-100 shadow border-2"
                            aria-label="Agregar {{ $producto->PRO_NOMBRE }} al carrito" data-bs-toggle="tooltip"
                            title="Agregar al carrito">
                            <i class="bi bi-cart-plus me-2" aria-hidden="true"></i>
                            Agregar al carrito
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-5 fw-bold w-100 border-2"
                            aria-label="Inicia sesión para comprar este producto">
                            <i class="bi bi-lock-fill me-2" aria-hidden="true"></i>
                            Inicia sesión para comprar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection