@extends('layouts.shop')

@section('content')
    <section class="min-vh-100 d-flex align-items-center justify-content-center hero-section"
        style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('img/background.jpg') }}') center/cover;">

        <div class="visually-hidden">
            Imagen de fondo: Persona usando gorra de lana en invierno.
        </div>

        <div class="container text-center text-white">
            <h1 class="display-2 fw-light text-uppercase mb-4 fw-bold text-light">
                La calidez se encuentra con el estilo
            </h1>
            <p class="fs-4 mb-5 text-light fw-bold">
                Gorras y gorros tejidos con materiales de la más alta calidad
            </p>

            <a href="{{ route('shop.products') }}"
                class="btn btn-light btn-lg px-5 text-uppercase fw-bold border-2 border-dark shadow">
                Ver productos
            </a>
        </div>
    </section>
@endsection