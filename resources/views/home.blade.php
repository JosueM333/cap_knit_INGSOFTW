@extends('layouts.app')

@section('content')
<div class="text-center mb-5">
    <h1 class="display-5 fw-light text-uppercase">Menú Principal</h1>
    <p class="text-muted">Panel de Administración General</p>
</div>

<div class="row row-cols-1 row-cols-md-2 g-4 justify-content-center" style="max-width: 900px; margin: 0 auto;">
    
    <div class="col">
        <a href="{{ route('clientes.index') }}" class="text-decoration-none text-dark">
            <div class="card card-menu h-100 border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="display-4 mb-3">👤</div> <h3 class="h4 text-uppercase">Gestión de Clientes</h3>
                    <p class="text-muted small">Registro, búsqueda y administración de usuarios.</p>
                    <span class="btn btn-outline-dark mt-2">Ingresar</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col">
        <a href="{{ route('proveedores.index') }}" class="text-decoration-none text-dark">
            <div class="card card-menu h-100 border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="display-4 mb-3">🏢</div>
                    <h3 class="h4 text-uppercase">Gestión de Proveedores</h3>
                    <p class="text-muted small">Administración de empresas suministradoras.</p>
                    <span class="btn btn-outline-dark mt-2">Ingresar</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col">
        <a href="{{ route('bodegas.index') }}" class="text-decoration-none text-dark">
            <div class="card card-menu h-100 border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="display-4 mb-3">📦</div>
                    <h3 class="h4 text-uppercase">Gestión de Bodega</h3>
                    <p class="text-muted small">Control de ubicaciones y almacenamiento físico.</p>
                    <span class="btn btn-outline-dark mt-2">Ingresar</span>
                </div>
            </div>
        </a>
    </div>

    <div class="col">
        <a href="{{ route('productos.index') }}" class="text-decoration-none text-dark">
            <div class="card card-menu h-100 border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <div class="display-4 mb-3">🧢</div>
                    <h3 class="h4 text-uppercase">Gestión de Productos</h3>
                    <p class="text-muted small">Catálogo de gorras, precios y stock.</p>
                    <span class="btn btn-outline-dark mt-2">Ingresar</span>
                </div>
            </div>
        </a>
    </div>

</div>
@endsection