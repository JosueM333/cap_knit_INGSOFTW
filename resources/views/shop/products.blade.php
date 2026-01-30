@extends('layouts.app')

@section('title', 'Colección - Cap & Knit')

@section('content')
    <div class="container py-4">
        <div class="text-center mb-5 animate-entry">
            <span class="text-accent text-uppercase fw-bold letter-spacing-2 small">Catálogo</span>
            <h1 class="display-4 fw-bold text-uppercase mb-3">Nuestra Colección</h1>
            <p class="fs-5 text-muted" style="max-width: 600px; margin: 0 auto;">
                Calidad artesanal y estilo moderno para mantenerte abrigado esta temporada.
            </p>
        </div>

        {{-- Filter/Search Section (Optional, nice to have) --}}
        <div class="row mb-5 justify-content-center">
            <div class="col-md-6">
                <form action="{{ route('shop.products') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-lg border-2"
                        placeholder="Buscar productos..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-premium btn-lg"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            @php
                $imagenesProductos = ['prod_1.png', 'prod_2.png', 'prod_3.png'];
            @endphp
            @forelse($productos as $loop => $producto)
                <div class="col-10 col-md-6 col-lg-3 animate-entry" style="animation-delay: {{ $loop->iteration * 0.1 }}s">

                    <article class="card card-premium h-100 d-flex flex-column">
                        <div class="product-img-wrapper bg-light">
                            {{-- Rotación de 3 imágenes --}}
                            <img src="{{ asset('img/' . $imagenesProductos[$loop->index % 3]) }}" class="w-100 h-100"
                                alt="Vista frontal de {{ $producto->PRO_NOMBRE }}"
                                onerror="this.src='{{ asset('img/cat_caps.png') }}'">

                            {{-- Badge Example --}}
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge bg-white text-dark shadow-sm">Nuevo</span>
                            </div>
                        </div>

                        <div class="card-body text-center d-flex flex-column p-4">
                            <h2 class="h5 card-title fw-bold mb-1">{{ $producto->PRO_NOMBRE }}</h2>
                            <p class="text-muted small mb-3">Ref: {{ $producto->PRO_CODIGO ?? $producto->PRO_ID }}</p>

                            <p class="fw-bold mb-0 fs-4 text-accent">
                                ${{ number_format($producto->PRO_PRECIO, 2) }}
                            </p>

                            <div class="mt-auto pt-4 d-grid gap-2">
                                <a href="{{ route('add.to.cart', $producto->PRO_ID) }}"
                                    class="btn btn-outline-dark fw-bold btn-sm text-uppercase" style="letter-spacing: 1px;">
                                    <i class="bi bi-bag-plus me-1"></i> Añadir
                                </a>

                                <a href="{{ route('shop.show', $producto->PRO_ID) }}"
                                    class="btn btn-premium btn-sm text-uppercase fs-6">
                                    Ver Detalle
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 animate-entry">
                    <div class="alert alert-light border border-secondary text-center fw-bold py-5 rounded-4" role="alert">
                        <i class="bi bi-info-circle fs-1 d-block mb-3 text-muted"></i>
                        <h3 class="h5">No hay productos disponibles por el momento.</h3>
                        <p class="mb-0">Vuelve a visitarnos pronto.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination if available --}}
        @if(method_exists($productos, 'links'))
            <div class="d-flex justify-content-center mt-5">
                {{ $productos->links() }}
            </div>
        @endif
    </div>
@endsection