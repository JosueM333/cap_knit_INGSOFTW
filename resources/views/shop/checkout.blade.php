@extends('layouts.shop')

@section('content')

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="mb-4 fw-bold text-uppercase h3">Finalizar Compra</h1>

                @if(session('error'))
                    <div class="alert alert-danger shadow-sm border-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="row g-5">
                    {{-- COLUMNA IZQUIERDA: Formulario de Pago --}}
                    <div class="col-md-7 order-md-1">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card-2-front me-2"></i> Método de Pago</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('shop.process_payment') }}" method="POST" id="payment-form">
                                    @csrf

                                    <div class="mb-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="paymentMethod"
                                                id="creditCard" value="credit" checked>
                                            <label class="form-check-label fw-bold" for="creditCard">Tarjeta
                                                Crédito/Débito</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="paymentMethod" id="paypal"
                                                value="paypal">
                                            <label class="form-check-label fw-bold" for="paypal">PayPal (Simulado)</label>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="cc-name" class="form-label fw-bold">Nombre en la tarjeta</label>
                                            <input type="text" class="form-control" id="cc-name" name="cc_name"
                                                placeholder="Ej. Juan Pérez" required>
                                            <div class="invalid-feedback">El nombre es requerido</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="cc-number" class="form-label fw-bold">Número de tarjeta</label>
                                            <input type="text" class="form-control" id="cc-number" name="cc_number"
                                                placeholder="0000 0000 0000 0000" maxlength="19" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="cc-expiration" class="form-label fw-bold">Vencimiento</label>
                                            <input type="text" class="form-control" id="cc-expiration" name="cc_expiration"
                                                placeholder="MM/YY" maxlength="5" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="cc-cvv" class="form-label fw-bold">CVV</label>
                                            <input type="text" class="form-control" id="cc-cvv" name="cc_cvv"
                                                placeholder="123" maxlength="4" required>
                                        </div>
                                    </div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" name="save_card" id="saveCard">
                                        <label class="form-check-label small" for="saveCard">
                                            Guardar esta tarjeta para futuras compras
                                        </label>
                                    </div>

                                    <hr class="my-4">

                                    <button class="btn btn-success w-100 btn-lg fw-bold shadow hover-scale" type="submit">
                                        <i class="bi bi-lock-fill me-2"></i> Pagar ${{ $total }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: Resumen --}}
                    <div class="col-md-5 order-md-2 mb-4">
                        <div class="card shadow-sm border-0 bg-light">
                            <div class="card-body p-4">
                                <h4 class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-primary fw-bold">Tu Carrito</span>
                                    <span class="badge bg-primary rounded-pill">{{ count($cart) }}</span>
                                </h4>
                                <ul class="list-group mb-3 border-0">
                                    @foreach($cart as $item)
                                        <li
                                            class="list-group-item d-flex justify-content-between lh-sm bg-transparent border-bottom">
                                            <div>
                                                <h6 class="my-0 fw-bold">{{ $item['name'] }}</h6>
                                                <small class="text-muted">Cantidad: {{ $item['quantity'] }}</small>
                                            </div>
                                            <span
                                                class="text-muted">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                        </li>
                                    @endforeach
                                    <li
                                        class="list-group-item d-flex justify-content-between bg-transparent fw-bold text-dark pt-3">
                                        <span>Total (USD)</span>
                                        <strong>${{ $total }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pequeño script para formatear tarjeta
        document.getElementById('cc-number').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d\s]/g, '').replace(/(\d{4})(?=\d)/g, '$1 ');
        });

        document.getElementById('cc-expiration').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d\/]/g, '').replace(/^(\d\d)(\d)$/g, '$1/$2').replace(/[^\d\/]/g, '');
        });
    </script>

@endsection