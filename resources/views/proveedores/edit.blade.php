@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Modificar Proveedor: {{ $proveedor->PRV_NOMBRE }}</h4>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Datos incorrectos o incompletos
                    </div>
                @endif
                
                <form action="{{ route('proveedores.update', $proveedor->PRV_ID) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">RUC (13 dígitos) *</label>
                            <input type="text" name="PRV_RUC" class="form-control @error('PRV_RUC') is-invalid @enderror" value="{{ old('PRV_RUC', $proveedor->PRV_RUC) }}" maxlength="13" required>
                            @error('PRV_RUC') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Razón Social / Nombre *</label>
                            <input type="text" name="PRV_NOMBRE" class="form-control @error('PRV_NOMBRE') is-invalid @enderror" value="{{ old('PRV_NOMBRE', $proveedor->PRV_NOMBRE) }}" required>
                            @error('PRV_NOMBRE') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Corporativo *</label>
                            <input type="email" name="PRV_EMAIL" class="form-control @error('PRV_EMAIL') is-invalid @enderror" value="{{ old('PRV_EMAIL', $proveedor->PRV_EMAIL) }}" required>
                            @error('PRV_EMAIL') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" name="PRV_TELEFONO" class="form-control @error('PRV_TELEFONO') is-invalid @enderror" value="{{ old('PRV_TELEFONO', $proveedor->PRV_TELEFONO) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección Física *</label>
                            <input type="text" name="PRV_DIRECCION" class="form-control @error('PRV_DIRECCION') is-invalid @enderror" value="{{ old('PRV_DIRECCION', $proveedor->PRV_DIRECCION) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Persona de Contacto</label>
                            <input type="text" name="PRV_PERSONA_CONTACTO" class="form-control" value="{{ old('PRV_PERSONA_CONTACTO', $proveedor->PRV_PERSONA_CONTACTO) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection