<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar Sesión - Cap & Knit | Acceso a tu Cuenta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        /* --- ESTILOS ORIGINALES TUYOS --- */
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .password-wrapper {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .login-header {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 0;
        }

        /* --- AGREGADO: ESTILOS PARA ALTO CONTRASTE (AA) --- */
        body.high-contrast {
            background-color: #000000 !important;
            color: #ffff00 !important;
        }
        
        body.high-contrast .login-header,
        body.high-contrast .card,
        body.high-contrast .bg-white,
        body.high-contrast .bg-light {
            background-color: #000000 !important;
            border-color: #ffff00 !important;
            color: #ffff00 !important;
        }

        body.high-contrast a, 
        body.high-contrast h1, 
        body.high-contrast label,
        body.high-contrast p,
        body.high-contrast .text-muted {
            color: #ffff00 !important;
        }

        body.high-contrast input {
            background-color: #000000 !important;
            color: #ffff00 !important;
            border: 2px solid #ffff00 !important;
        }

        body.high-contrast .btn-dark {
            background-color: #ffff00 !important;
            color: #000000 !important;
            border: 2px solid #ffff00 !important;
            font-weight: bold;
        }

        body.high-contrast .btn-outline-dark {
            color: #ffff00 !important;
            border-color: #ffff00 !important;
        }
        
        body.high-contrast .toggle-password {
            color: #ffff00 !important;
        }
    </style>
</head>

<body class="bg-light">

<header class="login-header" role="banner">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('shop.index') }}" class="text-dark text-decoration-none fw-bold fs-4">
            CAP &amp; KNIT
        </a>

        <button id="contrast-toggle"
                class="btn btn-sm btn-outline-dark"
                aria-pressed="false"
                aria-label="Activar modo de alto contraste">
            Alto contraste
        </button>
    </div>
</header>

<main id="main-content"
      class="d-flex justify-content-center align-items-center vh-100"
      tabindex="-1"
      role="main">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">

                <div class="card shadow p-4">
                    <h1 class="text-center mb-4">Iniciar Sesión</h1>

                    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label required-field">
                                Correo electrónico
                            </label>
                            <input type="email"
                                   id="email"
                                   name="CLI_EMAIL" 
                                   class="form-control @error('CLI_EMAIL') is-invalid @enderror"
                                   value="{{ old('CLI_EMAIL') }}"
                                   aria-required="true"
                                   aria-describedby="email-error"
                                   autocomplete="email"
                                   placeholder="ejemplo@correo.com"
                                   required>
                            
                            @error('CLI_EMAIL')
                                <div id="email-error" class="invalid-feedback d-block" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label required-field">
                                Contraseña
                            </label>
                            <div class="password-wrapper">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       aria-required="true"
                                       aria-describedby="password-error"
                                       autocomplete="current-password"
                                       placeholder="Ingresa tu contraseña"
                                       required>
                                <button type="button"
                                        class="toggle-password"
                                        aria-label="Mostrar contraseña"
                                        aria-pressed="false"
                                        onclick="togglePasswordVisibility()">
                                    Mostrar
                                </button>
                            </div>
                            @error('password')
                                <div id="password-error" class="invalid-feedback d-block" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember-me">
                                Recordar mis datos
                            </label>
                        </div>

                        <button type="submit"
                                class="btn btn-dark w-100 py-2 fw-bold"
                                id="submit-btn">
                            Entrar
                        </button>

                        <p class="form-text text-center mt-2">
                            Los campos marcados con * son obligatorios.
                        </p>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="mb-2">
                            <a href="#" class="text-decoration-none">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </p>
                        <p class="mb-0">
                            ¿No tienes cuenta?
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
                                Regístrate aquí
                            </a>
                        </p>
                    </div>
                </div>

                <div class="card mt-3 border-0 bg-transparent">
                    <div class="card-body text-center">
                        <p class="small text-muted mb-0">
                            Soporte:
                            <a href="mailto:soporte@capknit.com" class="text-decoration-none">
                                soporte@capknit.com
                            </a>
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<footer class="bg-white border-top py-4 mt-5" role="contentinfo">
    <div class="container text-center">
        <p class="mb-2 small text-muted">&copy; {{ date('Y') }} CAP &amp; KNIT.</p>
        <a href="#main-content" class="text-decoration-none small">
            Volver arriba
        </a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('accessibility.js') }}"></script>

<script>
    // Lógica para mostrar/ocultar contraseña (Tu código original)
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const button = document.querySelector('.toggle-password');

        if (input.type === 'password') {
            input.type = 'text';
            button.textContent = 'Ocultar';
            button.setAttribute('aria-pressed', 'true');
        } else {
            input.type = 'password';
            button.textContent = 'Mostrar';
            button.setAttribute('aria-pressed', 'false');
        }
    }
</script>

</body>
</html>