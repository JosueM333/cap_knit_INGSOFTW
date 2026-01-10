@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Registrar Nuevo Proveedor</h4>
            </div>
            <div class="card-body p-4">
                
                 @if ($errors->any())
                    <div class="alert alert-danger">
                        Datos incorrectos o incompletos
                    </div>
                @endif
                <form action="{{ route('proveedores.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">RUC (13 dígitos) *</label>
                            <input type="text" name="PRV_RUC" class="form-control @error('PRV_RUC') is-invalid @enderror" value="{{ old('PRV_RUC') }}" maxlength="13" required>
                            @error('PRV_RUC') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Razón Social / Nombre *</label>
                            <input type="text" name="PRV_NOMBRE" class="form-control @error('PRV_NOMBRE') is-invalid @enderror" value="{{ old('PRV_NOMBRE') }}" required>
                            @error('PRV_NOMBRE') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Corporativo *</label>
                            <input type="email" name="PRV_EMAIL" class="form-control @error('PRV_EMAIL') is-invalid @enderror" value="{{ old('PRV_EMAIL') }}" required>
                            @error('PRV_EMAIL') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" name="PRV_TELEFONO" class="form-control @error('PRV_TELEFONO') is-invalid @enderror" value="{{ old('PRV_TELEFONO') }}" required>
                            @error('PRV_TELEFONO') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección Física *</label>
                            <input type="text" name="PRV_DIRECCION" class="form-control @error('PRV_DIRECCION') is-invalid @enderror" value="{{ old('PRV_DIRECCION') }}" required>
                            @error('PRV_DIRECCION') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Persona de Contacto</label>
                            <input type="text" name="PRV_PERSONA_CONTACTO" class="form-control" value="{{ old('PRV_PERSONA_CONTACTO') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Guardar Proveedor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection