@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="h3 fw-bold mb-4 text-uppercase">Finalizar Compra / Checkout</h1>

        <div class="row">
            <!-- Columna Izquierda: Datos del Cliente y Método de Pago -->
            <div class="col-lg-6 mb-4">

                <!-- Datos del Cliente -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="bi bi-person-circle me-1"></i> Datos de Facturación y Envío
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Nombre Completo:</label>
                            <p class="mb-0 fs-5">{{ $user->CLI_NOMBRES }} {{ $user->CLI_APELLIDOS }}</p>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted">Cédula / RUC:</label>
                                <p class="mb-0">{{ $user->CLI_CEDULA }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small text-muted">Teléfono:</label>
                                <p class="mb-0">{{ $user->CLI_TELEFONO }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small text-muted">Dirección de Envío:</label>
                            <p class="mb-0">{{ $user->CLI_DIRECCION }}</p>
                        </div>
                        <div class="mb-0">
                            <label class="fw-bold small text-muted">Correo Electrónico:</label>
                            <p class="mb-0">{{ $user->CLI_EMAIL }}</p>
                        </div>
                    </div>
                </div>

                <!-- Método de Pago (Simulación) -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light fw-bold text-dark">
                        <i class="bi bi-credit-card me-1"></i> Método de Pago
                    </div>
                    <div class="card-body">
                        <form id="checkoutForm" action="{{ route('shop.placeOrder') }}" method="POST">
                            @csrf

                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="card" value="card"
                                    checked>
                                <label
                                    class="form-check-label w-100 d-flex justify-content-between align-items-center cursor-pointer"
                                    for="card">
                                    <span><i class="bi bi-credit-card-2-front me-2"></i> Tarjeta de Crédito / Débito</span>
                                    <div>
                                        <i class="bi bi-cc-visa fs-5 mx-1"></i>
                                        <i class="bi bi-cc-mastercard fs-5 mx-1"></i>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal"
                                    value="paypal">
                                <label
                                    class="form-check-label w-100 d-flex justify-content-between align-items-center cursor-pointer"
                                    for="paypal">
                                    <span><i class="bi bi-paypal me-2"></i> PayPal</span>
                                    <i class="bi bi-paypal fs-5 text-primary"></i>
                                </label>
                            </div>

                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="google"
                                    value="googlepay">
                                <label
                                    class="form-check-label w-100 d-flex justify-content-between align-items-center cursor-pointer"
                                    for="google">
                                    <span><i class="bi bi-google me-2"></i> Google Pay</span>
                                    <i class="bi bi-google fs-5 text-danger"></i>
                                </label>
                            </div>

                            <div class="alert alert-info mt-3 small mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Este es un entorno de demostración. No se realizará ningún cobro real.
                            </div>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Columna Derecha: Resumen del Pedido -->
            <div class="col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white fw-bold text-uppercase py-3">
                        <i class="bi bi-bag-check me-1"></i> Resumen del Pedido
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @php $total = 0; @endphp
                            @foreach($cart as $id => $details)
                                @php $total += $details['price'] * $details['quantity']; @endphp
                                <li class="list-group-item d-flex align-items-center gap-3 py-3">
                                    <div style="width: 60px; height: 60px; flex-shrink: 0;"
                                        class="bg-light rounded d-flex align-items-center justify-content-center overflow-hidden border">
                                        <img src="{{ asset($details['image']) }}" alt="{{ $details['name'] }}"
                                            class="w-100 h-100 object-fit-cover">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">{{ $details['name'] }}</h6>
                                        <small class="text-muted">Cant: {{ $details['quantity'] }} x
                                            ${{ number_format($details['price'], 2) }}</small>
                                    </div>
                                    <span
                                        class="fw-bold">${{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-light p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">IVA (15%):</span>
                            <span class="fw-bold">${{ number_format($total * 0.15, 2) }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-4 fw-bold text-dark">Total a Pagar:</span>
                            <span class="fs-3 fw-bold text-success">${{ number_format($total * 1.15, 2) }}</span>
                        </div>

                        <button type="submit" form="checkoutForm"
                            class="btn btn-dark w-100 py-3 fw-bold fs-5 shadow-sm text-uppercase">
                            <i class="bi bi-check-circle-fill me-2"></i> Finalizar Compra
                        </button>
                        <a href="{{ route('shop.cart') }}" class="d-block text-center mt-3 text-muted text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Volver al Carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection