@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                {{-- CAMBIO: Accedemos a la propiedad en minúsculas --}}
                <h4 class="mb-0 h5">Modificar Cliente: {{ $cliente->cli_nombres }}</h4>
            </div>
            <div class="card-body p-4">
                
                {{-- CAMBIO IMPORTANTE: 
                     1. Usamos ['id' => $cliente->cli_id] (minúscula) para que la ruta no falle.
                     2. Laravel ahora espera recibir el ID, no el objeto completo, gracias a tu config de rutas.
                --}}
                <form action="{{ route('clientes.update', ['id' => $cliente->cli_id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cédula *</label>
                            {{-- Todo a minúsculas: name, error, old, y propiedad del objeto --}}
                            <input type="text" name="cli_cedula" class="form-control @error('cli_cedula') is-invalid @enderror" value="{{ old('cli_cedula', $cliente->cli_cedula) }}" required maxlength="10">
                            @error('cli_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nombres *</label>
                            <input type="text" name="cli_nombres" class="form-control @error('cli_nombres') is-invalid @enderror" value="{{ old('cli_nombres', $cliente->cli_nombres) }}" required>
                            @error('cli_nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Apellidos *</label>
                            <input type="text" name="cli_apellidos" class="form-control @error('cli_apellidos') is-invalid @enderror" value="{{ old('cli_apellidos', $cliente->cli_apellidos) }}" required>
                            @error('cli_apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="cli_email" class="form-control @error('cli_email') is-invalid @enderror" value="{{ old('cli_email', $cliente->cli_email) }}" required>
                            @error('cli_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Teléfono *</label>
                            <input type="text" name="cli_telefono" class="form-control @error('cli_telefono') is-invalid @enderror" value="{{ old('cli_telefono', $cliente->cli_telefono) }}" required>
                            @error('cli_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Dirección *</label>
                            <input type="text" name="cli_direccion" class="form-control @error('cli_direccion') is-invalid @enderror" value="{{ old('cli_direccion', $cliente->cli_direccion) }}" required>
                            @error('cli_direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nueva Contraseña (Opcional)</label>
                            <input type="password" name="cli_password" class="form-control @error('cli_password') is-invalid @enderror" placeholder="Dejar en blanco para mantener actual">
                            @error('cli_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('clientes.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection