@extends('layouts.app')

@section('content')

    <div class="mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
        <h1 class="h3 text-uppercase">Kardex de Inventario</h1>
    </div>

    {{-- FILTROS --}}
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body py-3">
            <form action="{{ route('kardex.index') }}" method="GET" class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Bodega</label>
                    <select name="BOD_ID" class="form-select form-select-sm">
                        <option value="">-- Todas --</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->BOD_ID }}" {{ request('BOD_ID') == $bodega->BOD_ID ? 'selected' : '' }}>
                                {{ $bodega->BOD_NOMBRE }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Producto</label>
                    <select name="PRO_ID" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->PRO_ID }}" {{ request('PRO_ID') == $producto->PRO_ID ? 'selected' : '' }}>
                                {{ $producto->PRO_NOMBRE }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Tipo Movimiento</label>
                    <select name="TRA_ID" class="form-select form-select-sm">
                        <option value="">-- Todos --</option>
                        @foreach($transacciones as $transaccion)
                            <option value="{{ $transaccion->TRA_ID }}" {{ request('TRA_ID') == $transaccion->TRA_ID ? 'selected' : '' }}>
                                {{ $transaccion->TRA_CODIGO }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Fecha Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control form-control-sm"
                        value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-2">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark btn-sm">
                            <i class="bi bi-filter"></i> Filtrar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-sm">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Movimiento</th>
                        <th>Bodega</th>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th>Documento</th>
                        <th>Detalle/Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos as $mov)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($mov->KRD_FECHA)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($mov->transaccion->TRA_CODIGO == 'ENTRADA')
                                    <span class="badge bg-success">ENTRADA</span>
                                @elseif($mov->transaccion->TRA_CODIGO == 'SALIDA')
                                    <span class="badge bg-danger">SALIDA</span>
                                @else
                                    <span class="badge bg-primary">{{ $mov->transaccion->TRA_CODIGO }}</span>
                                @endif
                            </td>
                            <td>{{ $mov->bodega->BOD_NOMBRE }}</td>
                            <td>
                                <div class="fw-bold">{{ $mov->producto->PRO_NOMBRE }}</div>
                                <small class="text-muted">{{ $mov->producto->PRO_CODIGO }}</small>
                            </td>
                            <td class="text-center fs-6 fw-bold {{ $mov->KRD_CANTIDAD > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $mov->KRD_CANTIDAD > 0 ? '+' : '' }}{{ $mov->KRD_CANTIDAD }}
                            </td>
                            <td>
                                @if($mov->ORD_ID)
                                    <a href="{{ route('ordenes.show', $mov->ORD_ID) }}" class="text-decoration-none">
                                        OC #{{ $mov->ORD_ID }}
                                    </a>
                                @elseif($mov->COM_ID)
                                    <a href="{{ route('comprobantes.show', $mov->COM_ID) }}" class="text-decoration-none">
                                        FAC #{{ $mov->COM_ID }}
                                    </a>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td class="small text-muted">
                                <div>{{ $mov->KRD_OBSERVACION }}</div>
                                <div><i class="bi bi-person"></i> {{ $mov->KRD_USUARIO }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No hay movimientos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $movimientos->links() }}
        </div>
    </div>

@endsection