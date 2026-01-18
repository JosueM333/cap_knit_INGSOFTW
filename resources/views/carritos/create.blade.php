@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h1 class="h3 text-uppercase">Crear Carrito</h1>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        {{-- STORE → carritos.store --}}
        <form action="{{ route('carritos.store') }}"
              method="POST"
              class="row g-3">
            @csrf

            <div class="col-md-6">
                <label class="form-label">Cliente</label>
                <select name="CLI_ID" class="form-select" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach($clientes as $cli)
                        <option value="{{ $cli->CLI_ID }}">
                            {{ $cli->CLI_APELLIDOS }}
                            {{ $cli->CLI_NOMBRES }}
                            - {{ $cli->CLI_CEDULA }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select name="CRD_ESTADO" class="form-select" required>
                    <option value="ACTIVO">Activo</option>
                    <option value="CERRADO">Cerrado</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
            </div>

            {{-- Los totales NO se ingresan manualmente --}}
            {{-- Se calculan en el backend --}}
            <input type="hidden" name="CRD_SUBTOTAL" value="0">
            <input type="hidden" name="CRD_IMPUESTO" value="0">
            <input type="hidden" name="CRD_TOTAL" value="0">

            <div class="col-12 text-end mt-3">
                <a href="{{ route('carritos.index') }}"
                   class="btn btn-outline-secondary">
                    Cancelar
                </a>
                <button type="submit"
                        class="btn btn-dark">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
