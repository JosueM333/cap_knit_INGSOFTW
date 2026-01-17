@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3 text-uppercase">Crear Carrito</h1>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('carritos.store') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label">Cliente</label>
                <select name="CLI_ID" class="form-select" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->CLI_ID }}">
                            {{ $cli->CLI_APELLIDOS }} {{ $cli->CLI_NOMBRES }} - {{ $cli->CLI_CEDULA }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select name="CRD_ESTADO" class="form-select" required>
                    <option value="ABIERTO">Abierto</option>
                    <option value="CERRADO">Cerrado</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Subtotal</label>
                <input type="number" step="0.01" name="CRD_SUBTOTAL" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Impuesto</label>
                <input type="number" step="0.01" name="CRD_IMPUESTO" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Total</label>
                <input type="number" step="0.01" name="CRD_TOTAL" class="form-control" required>
            </div>

            <div class="col-12 text-end mt-3">
                <a href="{{ route('carritos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-dark">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
