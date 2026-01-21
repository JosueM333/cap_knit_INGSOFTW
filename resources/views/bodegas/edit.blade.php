@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Modificar Bodega: {{ $bodega->BOD_NOMBRE }}</h4>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Datos incorrectos o incompletos.
                    </div>
                @endif
                
                <form action="{{ route('bodegas.update', $bodega->BOD_ID) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nombre de la Bodega *</label>
                        <input type="text" name="BOD_NOMBRE" class="form-control @error('BOD_NOMBRE') is-invalid @enderror" value="{{ old('BOD_NOMBRE', $bodega->BOD_NOMBRE) }}" required>
                        @error('BOD_NOMBRE') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ubicación Física *</label>
                        <input type="text" name="BOD_UBICACION" class="form-control @error('BOD_UBICACION') is-invalid @enderror" value="{{ old('BOD_UBICACION', $bodega->BOD_UBICACION) }}" required>
                        @error('BOD_UBICACION') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Descripción</label>
                        <textarea name="BOD_DESCRIPCION" class="form-control @error('BOD_DESCRIPCION') is-invalid @enderror" rows="3">{{ old('BOD_DESCRIPCION', $bodega->BOD_DESCRIPCION) }}</textarea>
                        @error('BOD_DESCRIPCION') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('bodegas.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection