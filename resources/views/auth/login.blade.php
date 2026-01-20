<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Cap & Knit</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        .required-field::after { content: " *"; color: #dc3545; }
        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute; right: 10px; top: 50%;
            transform: translateY(-50%); background: none; border: none;
            cursor: pointer; font-size: 0.9rem; color: #6c757d;
        }
        .login-header { background-color: #ffffff; border-bottom: 1px solid #dee2e6; padding: 15px 0; }
        
        /* Alto contraste básico */
        body.high-contrast { background-color: #000; color: #ff0; }
        body.high-contrast .card, body.high-contrast input { background-color: #000; color: #ff0; border-color: #ff0; }
        body.high-contrast a, body.high-contrast .btn { color: #ff0; border-color: #ff0; }
    </style>
</head>

<body class="bg-light">

<header class="login-header">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('shop.index') }}" class="text-dark text-decoration-none fw-bold fs-4">CAP &amp; KNIT</a>
        <button id="contrast-toggle" class="btn btn-sm btn-outline-dark">Alto contraste</button>
    </div>
</header>

<main class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">

                <div class="card shadow p-4">
                    {{-- CAMBIO 1: Título genérico para todos --}}
                    <h1 class="text-center mb-4">Iniciar Sesión</h1>

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label required-field">Correo electrónico</label>
                            
                            {{-- Mantenemos name="email" (El controlador se encarga de traducirlo para clientes) --}}
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}" autofocus placeholder="ejemplo@correo.com">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label required-field">Contraseña</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">Mostrar</button>
                            </div>
                            
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember-me">Recordar mis datos</label>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 fw-bold">Entrar</button>
                    </form>

                    <div class="text-center mt-4 border-top pt-3">
                        {{-- CAMBIO 2: Enlace para que los clientes se registren --}}
                        <p class="mb-2">¿No tienes cuenta?</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary w-100 btn-sm fw-bold">Crear cuenta nueva</a>
                        
                        <div class="mt-3">
                             <a href="{{ route('shop.index') }}" class="text-decoration-none small text-muted">Volver a la tienda</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const btn = document.querySelector('.toggle-password');
        if (input.type === 'password') { input.type = 'text'; btn.textContent = 'Ocultar'; }
        else { input.type = 'password'; btn.textContent = 'Mostrar'; }
    }
    
    document.getElementById('contrast-toggle').addEventListener('click', function() {
        document.body.classList.toggle('high-contrast');
    });
</script>

</body>
</html>