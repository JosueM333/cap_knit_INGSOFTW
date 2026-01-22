@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3 text-uppercase">Crear Carrito Manualmente</h1>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('carritos.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label">Cliente *</label>
                <select name="CLI_ID" class="form-select" required>
                    <option value="">-- Seleccione un cliente --</option>
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->CLI_ID }}">
                            {{ $cli->CLI_APELLIDOS }} {{ $cli->CLI_NOMBRES }} ({{ $cli->CLI_CEDULA }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Estado Inicial</label>
                <input type="text" class="form-control" value="ACTIVO" readonly disabled>
                <input type="hidden" name="CRD_ESTADO" value="ACTIVO">
            </div>

            {{-- Los totales se inicializan en 0 automáticamente en el backend --}}

            <div class="col-12 text-end mt-4">
                <a href="{{ route('carritos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-dark">Crear Carrito</button>
            </div>
        </form>
    </div>
</div>
@endsection

