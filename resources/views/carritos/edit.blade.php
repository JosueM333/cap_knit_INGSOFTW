@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Gestionar Carrito #{{ $carrito->CRD_ID }}</h1>
    <a href="{{ route('carritos.consultar') }}" class="btn btn-outline-secondary">Volver al Listado</a>
</div>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

<div class="row">
    {{-- BUSCAR Y AGREGAR PRODUCTOS --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-dark text-white">Agregar Producto</div>
            <div class="card-body">
                <form action="{{ route('carritos.buscar_producto', $carrito->CRD_ID) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="criterio" class="form-control" placeholder="Nombre/Código" required>
                        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                    </div>
                </form>

                @if(isset($productos))
                <ul class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($productos as $prod)
                    <li class="list-group-item d-flex justify-content-between align-items-center p-2">
                        <div style="line-height: 1.2;">
                            <small class="fw-bold">{{ $prod->PRO_NOMBRE }}</small><br>
                            <small class="text-muted" style="font-size: 0.8rem;">
                                {{ $prod->PRO_MARCA ?? 'Genérico' }} - ${{ number_format($prod->PRO_PRECIO, 2) }}
                            </small>
                        </div>
                        <form action="{{ route('carritos.agregar_producto', $carrito->CRD_ID) }}" method="POST">
                            @csrf
                            <input type="hidden" name="PRO_ID" value="{{ $prod->PRO_ID }}">
                            <div class="input-group input-group-sm" style="width: 90px;">
                                <input type="number" name="DCA_CANTIDAD" value="1" min="1" class="form-control px-1 text-center">
                                <button class="btn btn-success" type="submit">+</button>
                            </div>
                        </form>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3">Producto no encontrado.</li>
                    @endforelse
                </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- DETALLES DEL CARRITO --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between">
                <span>Cliente: <strong>{{ $carrito->cliente->CLI_NOMBRES }} {{ $carrito->cliente->CLI_APELLIDOS }}</strong></span>
                <span class="badge bg-secondary">{{ $carrito->CRD_ESTADO }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th style="width: 140px;">Cantidad</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carrito->detalles as $detalle)
                        <tr>
                            <td>
                                {{ $detalle->producto->PRO_NOMBRE }}
                                @if($detalle->producto->PRO_TALLA)
                                    <br><small class="text-muted">Talla: {{ $detalle->producto->PRO_TALLA }}</small>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('carritos.actualizar_detalle', $detalle->DCA_ID) }}" method="POST" class="d-flex">
                                    @csrf @method('PATCH')
                                    <input type="number" name="cantidad" value="{{ $detalle->DCA_CANTIDAD }}" min="1" class="form-control form-control-sm me-1">
                                    <button type="submit" class="btn btn-sm btn-primary" title="Actualizar">OK</button>
                                </form>
                            </td>
                            <td class="text-end">${{ number_format($detalle->DCA_SUBTOTAL, 2) }}</td>
                            <td class="text-end">
                                <form action="{{ route('carritos.eliminar_detalle', $detalle->DCA_ID) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Quitar item?')">X</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Carrito vacío. Agregue productos desde el panel izquierdo.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Total a Pagar:</td>
                            <td class="text-end fw-bold fs-5">${{ number_format($carrito->CRD_TOTAL, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="card-footer bg-white text-end py-3">
                <form action="{{ route('carritos.guardar', $carrito->CRD_ID) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark btn-lg" {{ $carrito->detalles->isEmpty() ? 'disabled' : '' }}>
                        Guardar y Finalizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection