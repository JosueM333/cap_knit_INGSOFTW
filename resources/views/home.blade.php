@extends('layouts.app')

@section('content')
<div class="container py-5">
    
    <div class="mb-4">
        <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Volver a la Tienda Pública
        </a>
    </div>

    <div class="text-center mb-5">
        <h1 class="display-5 fw-light text-uppercase fw-bold">Menú Principal</h1>
        <p class="text-muted">Panel de Administración General</p>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center" style="max-width: 900px; margin: 0 auto;">
        
        <div class="col">
            <a href="{{ route('clientes.index') }}" class="text-decoration-none text-dark">
                <div class="card card-menu h-100 border-dark border-1 shadow-sm text-center py-5 transition-hover">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-people-fill display-4"></i>
                        </div>
                        <h3 class="h4 text-uppercase fw-bold">Gestión de Clientes</h3>
                        <p class="text-muted small">Registro, búsqueda y administración.</p>
                        <span class="btn btn-dark mt-2 fw-bold">Ingresar</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('proveedores.index') }}" class="text-decoration-none text-dark">
                <div class="card card-menu h-100 border-dark border-1 shadow-sm text-center py-5 transition-hover">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-buildings-fill display-4"></i>
                        </div>
                        <h3 class="h4 text-uppercase fw-bold">Gestión de Proveedores</h3>
                        <p class="text-muted small">Administración de empresas.</p>
                        <span class="btn btn-dark mt-2 fw-bold">Ingresar</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('bodegas.index') }}" class="text-decoration-none text-dark">
                <div class="card card-menu h-100 border-dark border-1 shadow-sm text-center py-5 transition-hover">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-box-seam-fill display-4"></i>
                        </div>
                        <h3 class="h4 text-uppercase fw-bold">Gestión de Bodega</h3>
                        <p class="text-muted small">Control de almacenamiento físico.</p>
                        <span class="btn btn-dark mt-2 fw-bold">Ingresar</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="{{ route('productos.index') }}" class="text-decoration-none text-dark">
                <div class="card card-menu h-100 border-dark border-1 shadow-sm text-center py-5 transition-hover">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-tags-fill display-4"></i>
                        </div>
                        <h3 class="h4 text-uppercase fw-bold">Gestión de Productos</h3>
                        <p class="text-muted small">Catálogo de gorras y stock.</p>
                        <span class="btn btn-dark mt-2 fw-bold">Ingresar</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col">
            <a href="{{ route('carritos.index') }}" class="text-decoration-none text-dark">
                <div class="card card-menu h-100 border-dark border-1 shadow-sm text-center py-5 transition-hover">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="bi bi-cart-fill display-4"></i>
                        </div>
                        <h3 class="h4 text-uppercase fw-bold">Gestión de Carritos</h3>
                        <p class="text-muted small">Creación, búsqueda y administración.</p>
                        <span class="btn btn-dark mt-2 fw-bold">Ingresar</span>
                    </div>
                </div>
            </a>
        </div>


    </div>
</div>

<style>
    /* Efecto hover simple para las tarjetas del menú */
    .transition-hover { transition: transform 0.2s; }
    .transition-hover:hover { transform: translateY(-5px); }
</style>
@endsection