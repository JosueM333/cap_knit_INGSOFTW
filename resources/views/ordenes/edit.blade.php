@extends('layouts.app')

@section('content')

<div class="mb-4 border-bottom pb-2">
    <h1 class="h3 text-uppercase">Modificar Orden #{{ $orden->ORD_ID }}</h1>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle"></i> Corrija los errores indicados.
    </div>
@endif

<form action="{{ route('ordenes.update', $orden->ORD_ID) }}" method="POST" id="formOrden">
    @csrf
    @method('PUT')

    {{-- DATOS DEL PROVEEDOR --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-primary"><i class="bi bi-person-badge"></i> Datos del Proveedor</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Proveedor</label>
                    <select name="PRV_ID" class="form-select" required>
                        @foreach($proveedores as $prv)
                            <option value="{{ $prv->PRV_ID }}" {{ $orden->PRV_ID == $prv->PRV_ID ? 'selected' : '' }}>
                                {{ $prv->PRV_NOMBRE }} (RUC: {{ $prv->PRV_RUC }})
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- ELIMINADO: OBSERVACIONES --}}
            </div>
        </div>
    </div>

    {{-- DETALLES DE PRODUCTOS --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-primary"><i class="bi bi-box-seam"></i> Detalle de Productos</h5>
        </div>
        
        <div class="card-body bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="small text-muted">Producto</label>
                    <select id="select_producto" class="form-select">
                        <option value="">-- Agregar Producto --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->PRO_ID }}" 
                                    data-nombre="{{ $prod->PRO_NOMBRE }}" 
                                    data-precio="{{ $prod->PRO_PRECIO }}">
                                {{ $prod->PRO_CODIGO }} - {{ $prod->PRO_NOMBRE }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small text-muted">Precio</label>
                    <input type="number" id="input_precio" class="form-control" placeholder="0.00" step="0.01">
                </div>
                <div class="col-md-2">
                    <label class="small text-muted">Cantidad</label>
                    <input type="number" id="input_cantidad" class="form-control" placeholder="1" min="1" value="1">
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-dark w-100" id="btnAgregar">
                        Agregar / Actualizar
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablaDetalles">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- JS llenará esto automáticamente --}}
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                        <td class="text-end fw-bold fs-5" id="totalGlobal">$ 0.00</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
        <a href="{{ route('ordenes.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
        <button type="submit" class="btn btn-primary px-5">Actualizar Orden</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let indice = 0;
    let totalOrden = 0;
    const tablaBody = document.querySelector('#tablaDetalles tbody');
    const labelTotal = document.getElementById('totalGlobal');
    
    function agregarFilaVisual(proId, nombre, cantidad, precio) {
        const subtotal = parseFloat(cantidad) * parseFloat(precio);
        totalOrden += subtotal;
        
        const fila = `
            <tr id="fila-${indice}">
                <td>
                    ${nombre}
                    <input type="hidden" name="detalles[${indice}][PRO_ID]" value="${proId}">
                </td>
                <td class="text-center">
                    ${cantidad}
                    <input type="hidden" name="detalles[${indice}][CANTIDAD]" value="${cantidad}">
                </td>
                <td class="text-end">
                    $ ${parseFloat(precio).toFixed(2)}
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
        actualizarTotal();
        indice++;
    }

    function actualizarTotal() {
        labelTotal.textContent = '$ ' + totalOrden.toFixed(2);
    }

    const detallesExistentes = @json($orden->detalles);
    
    detallesExistentes.forEach(det => {
        let nombreProd = 'Producto Eliminado';
        if(det.producto) {
            nombreProd = det.producto.PRO_NOMBRE; 
        } else {
            nombreProd = "ID: " + det.PRO_ID; 
        }
        agregarFilaVisual(det.PRO_ID, nombreProd, det.DOR_CANTIDAD, det.DOR_PRECIO);
    });

    document.getElementById('btnAgregar').addEventListener('click', function() {
        const sel = document.getElementById('select_producto');
        const cant = document.getElementById('input_cantidad').value;
        const prec = document.getElementById('input_precio').value;
        
        if(sel.value && cant > 0 && prec > 0) {
            const nombre = sel.options[sel.selectedIndex].text;
            agregarFilaVisual(sel.value, nombre, cant, prec);
            
            sel.value = ''; 
            document.getElementById('input_cantidad').value = 1; 
            document.getElementById('input_precio').value = '';
        } else { 
            alert('Seleccione un producto y verifique cantidad/precio.'); 
        }
    });
    
    document.getElementById('select_producto').addEventListener('change', function() {
        const op = this.options[this.selectedIndex];
        if(op.dataset.precio) {
            document.getElementById('input_precio').value = op.dataset.precio;
        }
    });

    window.eliminarFila = function(id, sub) {
        document.getElementById('fila-'+id).remove();
        totalOrden -= sub;
        actualizarTotal();
    };
});
</script>
@endsection