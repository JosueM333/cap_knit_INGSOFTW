@extends('layouts.app')

@section('content')

{{-- ENCABEZADO (Igual que tu diseño de referencia) --}}
<div class="mb-4 border-bottom pb-2">
    <h1 class="h3 text-uppercase">Generar Orden de Compra</h1>
</div>

{{-- BLOQUE DE MENSAJES (Validaciones del Backend y Excepciones E1/E3) --}}
@if(session('error'))
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> Por favor corrija los errores en el formulario.
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('ordenes.store') }}" method="POST" id="formOrden">
    @csrf

    {{-- PASO 5 (Parte A): SELECCIÓN DE PROVEEDOR --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-primary"><i class="bi bi-person-badge"></i> Datos del Proveedor</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="PRV_ID" class="form-label">Proveedor *</label>
                    <select name="PRV_ID" id="PRV_ID" class="form-select" required>
                        <option value="">-- Seleccione un Proveedor --</option>
                        @foreach($proveedores as $prv)
                            <option value="{{ $prv->PRV_ID }}" {{ old('PRV_ID') == $prv->PRV_ID ? 'selected' : '' }}>
                                {{ $prv->PRV_NOMBRE }} (RUC: {{ $prv->PRV_RUC }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fecha de Emisión</label>
                    <input type="text" class="form-control" value="{{ date('d/m/Y') }}" readonly disabled>
                    <small class="text-muted">La fecha se registra automáticamente al guardar.</small>
                </div>
            </div>
            
            {{-- Campo opcional para observaciones --}}
            <div class="mt-3">
                <label for="ORD_OBSERVACION" class="form-label">Observaciones</label>
                <textarea name="ORD_OBSERVACION" class="form-control" rows="2" placeholder="Notas adicionales sobre la orden...">{{ old('ORD_OBSERVACION') }}</textarea>
            </div>
        </div>
    </div>

    {{-- PASO 5 (Parte B): SELECCIÓN DE PRODUCTOS (Detalle) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary"><i class="bi bi-box-seam"></i> Detalle de Productos</h5>
        </div>
        
        <div class="card-body bg-light">
            {{-- Formulario "falso" para agregar ítems a la tabla (No se envía, usa JS) --}}
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small">Producto</label>
                    <select id="select_producto" class="form-select">
                        <option value="">-- Buscar Producto --</option>
                        @foreach($productos as $prod)
                            {{-- Guardamos el precio en data-precio como referencia --}}
                            <option value="{{ $prod->PRO_ID }}" data-nombre="{{ $prod->PRO_NOMBRE }}" data-precio="{{ $prod->PRO_PRECIO }}">
                                {{ $prod->PRO_CODIGO }} - {{ $prod->PRO_NOMBRE }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Precio Unit. ($)</label>
                    <input type="number" id="input_precio" class="form-control" step="0.01" min="0.01" placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Cantidad</label>
                    <input type="number" id="input_cantidad" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-dark w-100" id="btnAgregar">
                        <i class="bi bi-plus-lg"></i> Agregar
                    </button>
                </div>
            </div>
        </div>

        {{-- TABLA VISUAL DE ITEMS --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaDetalles">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Aquí se insertan las filas con JS --}}
                    <tr id="filaVacia">
                        <td colspan="5" class="text-center py-4 text-muted">
                            No hay productos en la orden. Agregue ítems arriba.
                        </td>
                    </tr>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAL ORDEN:</td>
                        <td class="text-end fw-bold fs-5" id="totalGlobal">$ 0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- PASO 6: BOTÓN GUARDAR --}}
    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
        <a href="{{ route('ordenes.index') }}" class="btn btn-outline-secondary me-md-2">Cancelar</a>
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-save"></i> Guardar Orden
        </button>
    </div>

</form>

{{-- SCRIPT PARA MANEJAR EL DETALLE (MASTER-DETAIL) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let indice = 0;
        let totalOrden = 0;

        const btnAgregar = document.getElementById('btnAgregar');
        const selectProducto = document.getElementById('select_producto');
        const inputCantidad = document.getElementById('input_cantidad');
        const inputPrecio = document.getElementById('input_precio');
        const tablaBody = document.querySelector('#tablaDetalles tbody');
        const filaVacia = document.getElementById('filaVacia');
        const labelTotal = document.getElementById('totalGlobal');

        // Al cambiar producto, poner su precio sugerido
        selectProducto.addEventListener('change', function() {
            const opcion = this.options[this.selectedIndex];
            if(opcion.dataset.precio) {
                inputPrecio.value = opcion.dataset.precio;
            } else {
                inputPrecio.value = '';
            }
        });

        // Evento Agregar
        btnAgregar.addEventListener('click', function() {
            // Validaciones simples JS
            const proId = selectProducto.value;
            const proNombre = selectProducto.options[selectProducto.selectedIndex].text;
            const cantidad = parseFloat(inputCantidad.value);
            const precio = parseFloat(inputPrecio.value);

            if(!proId || isNaN(cantidad) || cantidad < 1 || isNaN(precio) || precio <= 0) {
                alert('Por favor seleccione un producto, precio y cantidad válida.');
                return;
            }

            // Ocultar mensaje de vacío
            if(filaVacia) filaVacia.style.display = 'none';

            // Cálculos
            const subtotal = cantidad * precio;
            totalOrden += subtotal;

            // Crear Fila HTML
            // NOTA: Los inputs hidden son lo que Laravel recibirá en $request->detalles
            const fila = `
                <tr id="fila-${indice}">
                    <td>
                        ${proNombre}
                        <input type="hidden" name="detalles[${indice}][PRO_ID]" value="${proId}">
                    </td>
                    <td class="text-center">
                        ${cantidad}
                        <input type="hidden" name="detalles[${indice}][CANTIDAD]" value="${cantidad}">
                    </td>
                    <td class="text-end">
                        $ ${precio.toFixed(2)}
                        <input type="hidden" name="detalles[${indice}][PRECIO]" value="${precio}">
                    </td>
                    <td class="text-end fw-bold">
                        $ ${subtotal.toFixed(2)}
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFila(${indice}, ${subtotal})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            tablaBody.insertAdjacentHTML('beforeend', fila);
            
            // Actualizar Total Visual
            labelTotal.textContent = '$ ' + totalOrden.toFixed(2);

            // Limpiar inputs
            selectProducto.value = '';
            inputCantidad.value = 1;
            inputPrecio.value = '';
            indice++;
        });

        // Función global para eliminar fila
        window.eliminarFila = function(id, subtotal) {
            const fila = document.getElementById(`fila-${id}`);
            if(fila) {
                fila.remove();
                totalOrden -= subtotal;
                labelTotal.textContent = '$ ' + totalOrden.toFixed(2);
                
                // Si no quedan filas, mostrar aviso
                if(tablaBody.querySelectorAll('tr').length === 1) { // 1 porque filaVacia está oculta
                    if(filaVacia) filaVacia.style.display = 'table-row';
                }
            }
        };
    });
</script>

@endsection