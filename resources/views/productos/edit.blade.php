@extends('layouts.app')

@section('content')
{{-- BREADCRUMBS --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
        <li class="breadcrumb-item active" aria-current="page">Editar</li>
    </ol>
</nav>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Modificar Producto: {{ $producto->PRO_NOMBRE }}</h4>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Datos incorrectos o incompletos.
                    </div>
                @endif
                
                <form action="{{ route('productos.update', $producto->PRO_ID) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Código del Producto *</label>
                            <input type="text" name="PRO_CODIGO" class="form-control @error('PRO_CODIGO') is-invalid @enderror" value="{{ old('PRO_CODIGO', $producto->PRO_CODIGO) }}" required>
                            @error('PRO_CODIGO') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Proveedor *</label>
                            <select name="PRV_ID" class="form-select" required>
                                @foreach($proveedores as $prv)
                                    <option value="{{ $prv->PRV_ID }}" {{ $producto->PRV_ID == $prv->PRV_ID ? 'selected' : '' }}>
                                        {{ $prv->PRV_NOMBRE }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nombre del Producto *</label>
                            <input type="text" name="PRO_NOMBRE" class="form-control" value="{{ old('PRO_NOMBRE', $producto->PRO_NOMBRE) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="PRO_DESCRIPCION" class="form-control" rows="2" required>{{ old('PRO_DESCRIPCION', $producto->PRO_DESCRIPCION) }}</textarea>
                        </div>

                        {{-- Fila de atributos secundarios --}}
                        <div class="col-md-3">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" name="PRO_PRECIO" class="form-control" value="{{ old('PRO_PRECIO', $producto->PRO_PRECIO) }}" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="PRO_MARCA" class="form-control" value="{{ old('PRO_MARCA', $producto->PRO_MARCA) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Color</label>
                            <input type="text" name="PRO_COLOR" class="form-control" value="{{ old('PRO_COLOR', $producto->PRO_COLOR) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Talla</label>
                            <input type="text" name="PRO_TALLA" class="form-control" value="{{ old('PRO_TALLA', $producto->PRO_TALLA) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('productos.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection