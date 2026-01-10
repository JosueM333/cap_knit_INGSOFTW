@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h4 class="mb-0 h5">Registrar Nuevo Producto</h4>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info small mb-4">
                    <i class="bi bi-info-circle"></i> Al crear el producto, se asignará automáticamente a la Bodega Principal con stock 0.
                </div>

                 @if ($errors->any())
                    <div class="alert alert-danger">
                        Datos incorrectos o incompletos
                    </div>
                @endif

                <form action="{{ route('productos.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Código SKU *</label>
                            <input type="text" name="PRO_CODIGO" class="form-control @error('PRO_CODIGO') is-invalid @enderror" value="{{ old('PRO_CODIGO') }}" required>
                            @error('PRO_CODIGO') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Proveedor *</label>
                            <select name="PRV_ID" class="form-select @error('PRV_ID') is-invalid @enderror" required>
                                <option value="">-- Seleccione Proveedor --</option>
                                @foreach($proveedores as $prv)
                                    <option value="{{ $prv->PRV_ID }}" {{ old('PRV_ID') == $prv->PRV_ID ? 'selected' : '' }}>
                                        {{ $prv->PRV_NOMBRE }}
                                    </option>
                                @endforeach
                            </select>
                            @error('PRV_ID') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Nombre del Producto *</label>
                            <input type="text" name="PRO_NOMBRE" class="form-control @error('PRO_NOMBRE') is-invalid @enderror" value="{{ old('PRO_NOMBRE') }}" required>
                            @error('PRO_NOMBRE') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción Detallada *</label>
                            <textarea name="PRO_DESCRIPCION" class="form-control @error('PRO_DESCRIPCION') is-invalid @enderror" rows="2" required>{{ old('PRO_DESCRIPCION') }}</textarea>
                            @error('PRO_DESCRIPCION') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Precio ($) *</label>
                            <input type="number" step="0.01" name="PRO_PRECIO" class="form-control @error('PRO_PRECIO') is-invalid @enderror" value="{{ old('PRO_PRECIO') }}" required>
                            @error('PRO_PRECIO') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" name="PRO_MARCA" class="form-control" value="{{ old('PRO_MARCA') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Color</label>
                            <input type="text" name="PRO_COLOR" class="form-control" value="{{ old('PRO_COLOR') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('productos.index') }}" class="btn btn-light border">Cancelar</a>
                        <button type="submit" class="btn btn-dark">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection