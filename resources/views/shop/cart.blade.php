@extends('layouts.shop')

@section('styles')
    <style>
        /* Accesibilidad: Aumentar contraste para cumplir AAA (Ratio > 7:1) */

        /* BOTONES DE ACCIÓN (Verde y Rojos) */
        /* Verde: Textos y Botones - Ratio > 7:1 */
        .text-success {
            color: #0a3622 !important;
        }

        .btn-success {
            background-color: #0a3622 !important;
            border-color: #0a3622 !important;
            color: #ffffff !important;
        }

        .btn-success:hover {
            background-color: #000000 !important;
            border-color: #000000 !important;
            color: #ffffff !important;
        }

        /* Rojo: Botones de Borde (Danger) */
        .btn-outline-danger {
            color: #700000 !important;
            border-color: #700000 !important;
            background-color: transparent !important;
        }

        .btn-outline-danger:hover {
            background-color: #700000 !important;
            color: #ffffff !important;
            border-color: #700000 !important;
        }

        .btn-outline-danger i {
            color: inherit !important;
        }

        /* AZUL: Enlaces dentro del contenido principal SOLAMENTE */
        /* Esto evita romper el header oscuro donde los enlaces deben ser blancos */
        main a:not(.btn),
        main .text-primary {
            color: #002a5c !important;
        }

        main a:not(.btn):hover {
            color: #001835 !important;
        }

        /* Enlaces secundarios (text-dark) dentro del main tambien deben ser oscuros */
        main a.text-dark {
            color: #000000 !important;
        }
    </style>
@endsection

@section('content')
    <div class="container my-5 flex-grow-1">

        @if(session('success'))
            <div class="alert alert-success fw-bold border-2 border-success shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger fw-bold border-2 border-danger shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i> {{ session('error') }}
            </div>
        @endif

        <h1 class="mb-4 fw-bold text-uppercase display-5">Tu Carrito</h1>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="table-responsive">
                <table class="table table-bordered align-middle shadow-sm">
                    <thead class="table-light border-dark">
                        <tr>
                            <th scope="col" style="width:35%">Producto</th>
                            <th scope="col" style="width:15%">Precio</th>
                            <th scope="col" style="width:20%" class="text-center">Cantidad</th>
                            <th scope="col" style="width:15%" class="text-center">Subtotal</th>
                            <th scope="col" style="width:15%" class="text-center">Borrar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach(session('cart') as $id => $item)
                            @php
                                $subtotalItem = $item['price'] * $item['quantity'];
                                $total += $subtotalItem;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ asset($item['image'] ?? 'img/productos/' . ($item['code'] ?? '00000') . '.jpg') }}"
                                            alt="{{ $item['name'] }}" class="rounded border border-dark" width="60" height="60"
                                            aria-hidden="true"
                                            onerror="this.onerror=null; this.src='{{ asset('img/productos/gorranewera.jpg') }}';">
                                        <div class="fw-bold">{{ $item['name'] }}</div>
                                    </div>
                                </td>

                                <td class="fw-bold">
                                    <span class="visually-hidden">Precio unitario </span>
                                    ${{ number_format($item['price'], 2) }}
                                </td>

                                <td class="text-center">
                                    <form action="{{ route('update.cart') }}" method="POST"
                                        class="d-inline-flex align-items-center gap-1 ajax-cart-form" onsubmit="return false;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="id" value="{{ $id }}">

                                        <button type="button" class="btn btn-outline-dark btn-sm fw-bold px-3 qty-btn-minus"
                                            onclick="updateQty('{{ $id }}', -1)" aria-label="Menos {{ $item['name'] }}">
                                            <span aria-hidden="true">−</span>
                                        </button>

                                        <label for="qty-{{ $id }}" class="visually-hidden">Cantidad</label>
                                        <input type="number" id="qty-{{ $id }}"
                                            class="form-control text-center fw-bold border-dark mx-1 qty-input" name="quantity"
                                            value="{{ $item['quantity'] }}" min="1" max="10" style="width:60px" data-id="{{ $id }}">

                                        <button type="button" class="btn btn-outline-dark btn-sm fw-bold px-3 qty-btn-plus"
                                            onclick="updateQty('{{ $id }}', 1)" aria-label="Más {{ $item['name'] }}">
                                            <span aria-hidden="true">+</span>
                                        </button>
                                    </form>
                                </td>

                                <td class="text-center fw-bold text-success">
                                    <span class="visually-hidden">Subtotal </span>
                                    $<span id="subtotal-item-{{ $id }}">{{ number_format($subtotalItem, 2) }}</span>
                                </td>

                                <td class="text-center">
                                    <form action="{{ route('remove.from.cart') }}" method="POST" id="delete-form-{{ $id }}">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="button" class="btn btn-outline-danger btn-sm border-2"
                                            aria-label="Quitar {{ $item['name'] }}" data-bs-toggle="tooltip"
                                            title="Eliminar producto" onclick="showDeleteModal('{{ $id }}')">
                                            <i class="bi bi-trash-fill" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="p-0 border-0">
                                <div class="row justify-content-end p-4">
                                    <div class="col-md-5 border border-2 border-dark rounded p-4 bg-white shadow-sm">
                                        <h2 class="h5 fw-bold mb-3 border-bottom border-dark pb-2">Resumen</h2>
                                        @php
                                            $iva = $total * config('shop.iva');
                                            $totalPagar = $total + $iva;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span class="fw-bold">$<span
                                                    id="cart-subtotal">{{ number_format($total, 2) }}</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>IVA ({{ config('shop.iva') * 100 }}%):</span>
                                            <span class="fw-bold">$<span
                                                    id="cart-iva">{{ number_format($iva, 2) }}</span></span>
                                        </div>

                                        <div class="d-flex justify-content-between border-top border-dark pt-3 mb-4">
                                            <span class="h5 fw-bold mb-0">Total:</span>
                                            <span class="h5 fw-bold mb-0 text-success">$<span
                                                    id="cart-total">{{ number_format($totalPagar, 2) }}</span></span>
                                        </div>

                                        <div class="d-grid gap-2 mt-4">
                                            <a href="{{ route('shop.checkout') }}"
                                                class="btn btn-success fw-bold w-100 btn-lg shadow border-2 border-success text-uppercase">
                                                Proceder al Pago
                                            </a>

                                            <a href="{{ route('shop.products') }}" class="btn btn-outline-dark fw-bold w-100">
                                                Seguir Comprando
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- MODAL CONFIRMACION MONTO ALTO --}}
            <div class="modal fade" id="highAmountModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-warning">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Confirmación de
                                Seguridad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <p class="lead">El monto total a pagar es superior a <strong>$5,000.00</strong>.</p>
                            <p class="mb-0">¿Confirma que desea procesar esta transacción por
                                <strong>${{ number_format($totalPagar, 2) }}</strong>?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-warning fw-bold" onclick="forceSubmit()">Confirmar y
                                Pagar</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL ELIMINACION --}}
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-danger">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Eliminar Producto
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <p class="lead">¿Está seguro de eliminar este producto del carrito?</p>
                            <p class="mb-0 text-muted small">Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger fw-bold" onclick="confirmDelete()">Confirmar
                                Eliminación</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 border border-2 border-dark rounded shadow-sm bg-light">
                <i class="bi bi-cart-x fs-1 text-muted" aria-hidden="true"></i>
                <h2 class="fw-bold mt-3 h3">Carrito vacío</h2>
                <a href="{{ route('shop.products') }}" class="btn btn-dark px-5 fw-bold shadow border-2 mt-3">
                    Ver Productos
                </a>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('cart-ajax.js') }}"></script>
    <script>
        let deleteItemId = null;

        function showDeleteModal(id) {
            deleteItemId = id;
            var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            myModal.show();
        }

        function confirmDelete() {
            if (deleteItemId) {
                document.getElementById('delete-form-' + deleteItemId).submit();
            }
        }

        function handlePurchase(event) {
            // Obsolete in this view but kept for reference if needed
            // Logic moved to Checkout page
        }

        function forceSubmit() {
            // Logic moved to Checkout page
        }
    </script>
@endsection