@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Registrar Nuevo Cliente</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cédula *</label>
                            {{-- Cambiado a minúsculas: cli_cedula --}}
                            <input type="text" name="cli_cedula" class="form-control @error('cli_cedula') is-invalid @enderror" value="{{ old('cli_cedula') }}" required maxlength="10">
                            @error('cli_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombres *</label>
                            <input type="text" name="cli_nombres" class="form-control @error('cli_nombres') is-invalid @enderror" value="{{ old('cli_nombres') }}" required>
                            @error('cli_nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="cli_apellidos" class="form-control @error('cli_apellidos') is-invalid @enderror" value="{{ old('cli_apellidos') }}" required>
                            @error('cli_apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="cli_email" class="form-control @error('cli_email') is-invalid @enderror" value="{{ old('cli_email') }}" required>
                            @error('cli_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" name="cli_telefono" class="form-control @error('cli_telefono') is-invalid @enderror" value="{{ old('cli_telefono') }}" required>
                            @error('cli_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección *</label>
                            <input type="text" name="cli_direccion" class="form-control @error('cli_direccion') is-invalid @enderror" value="{{ old('cli_direccion') }}" required>
                            @error('cli_direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" name="cli_password" class="form-control @error('cli_password') is-invalid @enderror" required>
                            @error('cli_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
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