@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">Modificar Comprobante</h1>
        <a href="{{ route('comprobantes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Cancelar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 fw-bold">Edición de Comprobante #{{ str_pad($comprobante->COM_ID, 6, '0', STR_PAD_LEFT) }}</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('comprobantes.update', $comprobante->COM_ID) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-warning small border-warning text-dark">
                            <i class="bi bi-lock-fill me-1"></i> 
                            <strong>Nota:</strong> Los campos fiscales y financieros están bloqueados por seguridad.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cliente</label>
                                <input type="text" class="form-control bg-light" value="{{ $comprobante->cliente->CLI_NOMBRES }} {{ $comprobante->cliente->CLI_APELLIDOS }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Cédula / RUC</label>
                                <input type="text" class="form-control bg-light" value="{{ $comprobante->cliente->CLI_CEDULA }}" readonly disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha Emisión</label>
                                <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($comprobante->COM_FECHA)->format('d/m/Y') }}" readonly disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">IVA (15%)</label>
                                <input type="text" class="form-control bg-light" value="${{ number_format($comprobante->COM_IVA, 2) }}" readonly disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-primary">Total Factura</label>
                                <input type="text" class="form-control bg-light fw-bold text-primary" value="${{ number_format($comprobante->COM_TOTAL, 2) }}" readonly disabled>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label for="observaciones" class="form-label fw-bold">Observaciones (Editable)</label>
                            <textarea class="form-control border-primary" name="observaciones" id="observaciones" rows="3">{{ old('observaciones', $comprobante->COM_OBSERVACIONES) }}</textarea>
                            <div class="form-text">Puede modificar detalles de entrega o notas internas.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                <i class="bi bi-save me-2"></i> Actualizar Comprobante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection