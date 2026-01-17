@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3 text-uppercase">Editar Carrito</h1>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('carritos.update', $carrito->CRD_ID) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label class="form-label">Cliente</label>
                <select name="CLI_ID" class="form-select" disabled>
                    <option>
                        {{ $carrito->cliente->CLI_APELLIDOS }} {{ $carrito->cliente->CLI_NOMBRES }}
                    </option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select name="CRD_ESTADO" class="form-select">
                    <option value="ABIERTO" {{ $carrito->CRD_ESTADO=='ABIERTO'?'selected':'' }}>Abierto</option>
                    <option value="CERRADO" {{ $carrito->CRD_ESTADO=='CERRADO'?'selected':'' }}>Cerrado</option>
                    <option value="CANCELADO" {{ $carrito->CRD_ESTADO=='CANCELADO'?'selected':'' }}>Cancelado</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Subtotal</label>
                <input type="number" step="0.01" name="CRD_SUBTOTAL"
                       value="{{ $carrito->CRD_SUBTOTAL }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Impuesto</label>
                <input type="number" step="0.01" name="CRD_IMPUESTO"
                       value="{{ $carrito->CRD_IMPUESTO }}" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Total</label>
                <input type="number" step="0.01" name="CRD_TOTAL"
                       value="{{ $carrito->CRD_TOTAL }}" class="form-control">
            </div>

            <div class="col-12 text-end mt-3">
                <a href="{{ route('carritos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-dark">Actualizar</button>
            </div>
        </form>
    </div>
</div>
@endsection
