@extends('layouts.shop')

@section('content')
    <div class="container py-5 my-4">

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
    </div>
@endsection