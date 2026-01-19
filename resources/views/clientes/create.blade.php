@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Registrar Nuevo Cliente</h4>
            </div>
            <div class="card-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        Datos incorrectos o incompletos
                    </div>
                @endif

                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cédula *</label>
                            <input type="text" name="CLI_CEDULA" class="form-control @error('CLI_CEDULA') is-invalid @enderror" value="{{ old('CLI_CEDULA') }}" required maxlength="10">
                            @error('CLI_CEDULA') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombres *</label>
                            <input type="text" name="CLI_NOMBRES" class="form-control @error('CLI_NOMBRES') is-invalid @enderror" value="{{ old('CLI_NOMBRES') }}" required>
                             @error('CLI_NOMBRES') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="CLI_APELLIDOS" class="form-control @error('CLI_APELLIDOS') is-invalid @enderror" value="{{ old('CLI_APELLIDOS') }}" required>
                             @error('CLI_APELLIDOS') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="CLI_EMAIL" class="form-control @error('CLI_EMAIL') is-invalid @enderror" value="{{ old('CLI_EMAIL') }}" required>
                             @error('CLI_EMAIL') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" name="CLI_TELEFONO" class="form-control @error('CLI_TELEFONO') is-invalid @enderror" value="{{ old('CLI_TELEFONO') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección *</label>
                            <input type="text" name="CLI_DIRECCION" class="form-control @error('CLI_DIRECCION') is-invalid @enderror" value="{{ old('CLI_DIRECCION') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" name="CLI_PASSWORD" class="form-control @error('CLI_PASSWORD') is-invalid @enderror" required>
                             @error('CLI_PASSWORD') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    
                  

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('clientes.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
