<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Token Global -->
    <title>Carrito - Cap & Knit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    
    <style>
        /* Accesibilidad: Aumentar contraste para cumplir AAA (Ratio > 7:1) */
        
        /* BOTONES DE ACCIÓN (Verde y Rojos) */
        /* Verde: Textos y Botones - Ratio > 7:1 */
        .text-success { color: #0a3622 !important; } 
        
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
        .btn-outline-danger i { color: inherit !important; }

        /* AZUL: Enlaces dentro del contenido principal SOLAMENTE */
        /* Esto evita romper el header oscuro donde los enlaces deben ser blancos */
        main a:not(.btn), main .text-primary { 
            color: #002a5c !important; 
        }
        main a:not(.btn):hover { 
            color: #001835 !important; 
        }
        
        /* Enlaces secundarios (text-dark) dentro del main tambien deben ser oscuros */
        main a.text-dark { color: #000000 !important; }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <header class="u-bg-gray border-bottom border-2 sticky-top py-3 shadow-sm" role="banner">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('shop.index') }}" class="text-white fw-bold fs-4 text-decoration-none d-flex align-items-center">
                <span class="logo-text text-white">CAP & KNIT</span>
            </a>
            <nav aria-label="Navegación principal">
                <ul class="d-flex gap-4 list-unstyled mb-0 fw-bold align-items-center text-uppercase small">
                    <li>
                        <a href="{{ route('shop.products') }}" class="text-white text-decoration-none hover-border-light">Productos</a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cart') }}" class="text-white text-decoration-none border-bottom border-light border-2 pb-1" aria-current="page"
                           data-bs-toggle="tooltip" title="Ver Carrito">
                            <i class="bi bi-cart-fill" aria-hidden="true"></i> 
                            Carrito ({{ count(session('cart', [])) }})
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" class="container my-5 flex-grow-1" tabindex="-1">
        
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
                                         alt="{{ $item['name'] }}" 
                                         class="rounded border border-dark" 
                                         width="60" height="60"
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
                                    <form action="{{ route('update.cart') }}" method="POST" class="d-inline-flex align-items-center gap-1 ajax-cart-form" onsubmit="return false;">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        
                                        <button type="button" 
                                                class="btn btn-outline-dark btn-sm fw-bold px-3 qty-btn-minus"
                                                onclick="updateQty('{{ $id }}', -1)"
                                                aria-label="Menos {{ $item['name'] }}">
                                            <span aria-hidden="true">−</span>
                                        </button>
                                        
                                        <label for="qty-{{ $id }}" class="visually-hidden">Cantidad</label>
                                        <input type="number" id="qty-{{ $id }}" 
                                               class="form-control text-center fw-bold border-dark mx-1 qty-input" 
                                               name="quantity"
                                               value="{{ $item['quantity'] }}" 
                                               min="1" max="10"
                                               style="width:60px"
                                               data-id="{{ $id }}">
                                        
                                        <button type="button" 
                                                class="btn btn-outline-dark btn-sm fw-bold px-3 qty-btn-plus"
                                                onclick="updateQty('{{ $id }}', 1)"
                                                aria-label="Más {{ $item['name'] }}">
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
                                                aria-label="Quitar {{ $item['name'] }}"
                                                data-bs-toggle="tooltip" title="Eliminar producto"
                                                onclick="showDeleteModal('{{ $id }}')">
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
                                            $iva = $total * 0.15;
                                            $totalPagar = $total + $iva;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span class="fw-bold">$<span id="cart-subtotal">{{ number_format($total, 2) }}</span></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span>IVA ({{ config('shop.iva') * 100 }}%):</span>
                                            <span class="fw-bold">$<span id="cart-iva">{{ number_format($iva, 2) }}</span></span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between border-top border-dark pt-3 mb-4">
                                            <span class="h5 fw-bold mb-0">Total:</span>
                                            <span class="h5 fw-bold mb-0 text-success">$<span id="cart-total">{{ number_format($totalPagar, 2) }}</span></span>
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-4">
                                            <form action="{{ route('shop.comprar') }}" method="POST" id="formCompra" onsubmit="return handlePurchase(event)">
                                                @csrf
                                                <button type="submit" class="btn btn-success fw-bold w-100 btn-lg shadow border-2 border-success text-uppercase">
                                                    Pagar Compra
                                                </button>
                                            </form>
                                            
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
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Confirmación de Seguridad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <p class="lead">El monto total a pagar es superior a <strong>$5,000.00</strong>.</p>
                            <p class="mb-0">¿Confirma que desea procesar esta transacción por <strong>${{ number_format($totalPagar, 2) }}</strong>?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-warning fw-bold" onclick="forceSubmit()">Confirmar y Pagar</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL ELIMINACION --}}
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-danger">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Eliminar Producto</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4">
                            <p class="lead">¿Está seguro de eliminar este producto del carrito?</p>
                            <p class="mb-0 text-muted small">Esta acción no se puede deshacer.</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger fw-bold" onclick="confirmDelete()">Confirmar Eliminación</button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Inicializar tooltips
                document.addEventListener('DOMContentLoaded', function () {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    })
                });

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
                    event.preventDefault(); // Siempre prevenimos el submit estándar para manejarlo manualmente

                    // Obtener total
                    const totalElement = document.getElementById('cart-total-display');
                    let total = 0;
                    if (totalElement) {
                        total = parseFloat(totalElement.getAttribute('data-amount')) || 
                                parseFloat(totalElement.textContent.replace(/[$,]/g, '')) || 0;
                    }

                    if (total > 5000) {
                        var myModal = new bootstrap.Modal(document.getElementById('highAmountModal'));
                        myModal.show();
                        return false;
                    }

                    // Si no es monto alto, procesar directamente
                    processPopupSubmission();
                }

                function forceSubmit() {
                    // Cerrar modal
                    const modalEl = document.getElementById('highAmountModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    processPopupSubmission();
                }

                function processPopupSubmission() {
                    const form = document.getElementById('formCompra');
                    // Envio directo en la misma ventana
                    form.submit();
                }
            </script>
        @else
            <div class="text-center py-5 border border-2 border-dark rounded shadow-sm bg-light">
                <i class="bi bi-cart-x fs-1 text-muted" aria-hidden="true"></i>
                <h2 class="fw-bold mt-3 h3">Carrito vacío</h2>
                <a href="{{ route('shop.products') }}" class="btn btn-dark px-5 fw-bold shadow border-2 mt-3">
                    Ver Productos
                </a>
            </div>
        @endif
    </main>

    <footer class="u-bg-gray border-top border-2 border-dark py-3" role="contentinfo">
        <div class="container text-center text-md-start">
            <p class="mb-0 fw-bold text-white">&copy; {{ date('Y') }} Cap & Knit.</p>
        </div>
    </footer>

    <div class="accessibility-widget">
        <div id="access-menu" class="access-menu p-3" hidden>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h2 class="h6 fw-bold mb-0 text-dark">Accesibilidad</h2>
                <button type="button" class="btn-close" aria-label="Cerrar menú" onclick="document.getElementById('access-btn').click()"></button>
            </div>
            
            <div class="mb-3">
                <span class="d-block small fw-bold mb-2 text-dark">Tamaño de texto</span>
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-decrease-font" aria-label="Disminuir">A-</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-reset-font" aria-label="Normal">A</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-increase-font" aria-label="Aumentar">A+</button>
                </div>
            </div>

            <div class="mb-3">
                <button id="contrast-toggle-widget" class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Alto Contraste</span>
                    <i class="bi bi-circle-half"></i>
                </button>
            </div>

            <div class="mb-1">
                <button id="links-toggle" class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Subrayar Enlaces</span>
                    <i class="bi bi-type-underline"></i>
                </button>
            </div>
        </div>

        <button id="access-btn" class="btn btn-dark rounded-circle shadow-lg p-3 mt-2" 
                aria-label="Abrir menú de accesibilidad" 
                aria-expanded="false" 
                aria-controls="access-menu">
            <i class="bi bi-universal-access-circle fs-2"></i>
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>
    <script src="{{ asset('cart-ajax.js') }}"></script>
    <script>
        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function showDeleteModal(id) {
            if(confirm('¿Estás seguro de eliminar este producto?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
</html>