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
        /* Estilos específicos solo para el login (el resto está en styles.css) */
        .login-header {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 0;
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
            font-weight: bold;
            z-index: 10;
        }

        /* Ajuste para que el texto no se monte sobre el botón */
        #password {
            padding-right: 80px;
        }
    </style>
</head>

<body class="bg-light">

    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <header class="login-header shadow-sm">
        <div class="container d-flex justify-content-center justify-content-md-start">
            <a href="{{ route('shop.index') }}"
                class="text-dark text-decoration-none fw-bold fs-4 d-flex align-items-center gap-2">
                <span class="logo-text">CAP & KNIT</span>
            </a>
        </div>
    </header>

    <main id="main-content" class="d-flex justify-content-center align-items-center min-vh-100 py-5" tabindex="-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5 col-xl-4">

                    <div class="card shadow border-0 rounded-3">
                        <div class="card-body p-4 p-md-5">

                            <h1 class="text-center mb-4 fw-bold h2">Iniciar Sesión</h1>

                            <form method="POST" action="{{ route('login') }}" id="loginForm">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        Correo electrónico <span class="text-danger" aria-hidden="true">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        required autocomplete="email" value="{{ old('email') }}"
                                        placeholder="ejemplo@correo.com" aria-required="true">

                                    @error('email')
                                        <span class="invalid-feedback fw-bold" role="alert">
                                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold">
                                        Contraseña <span class="text-danger" aria-hidden="true">*</span>
                                    </label>
                                    <div class="password-wrapper">
                                        <input type="password" id="password" name="password"
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
                                            required autocomplete="current-password" aria-required="true">

                                        <button type="button" class="toggle-password text-decoration-underline"
                                            onclick="togglePasswordVisibility()" aria-label="Mostrar contraseña"
                                            data-bs-toggle="tooltip" title="Mostrar/Ocultar contraseña">
                                            Mostrar
                                        </button>
                                    </div>

                                    @error('password')
                                        <span class="invalid-feedback d-block fw-bold" role="alert">
                                            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input border-dark" id="remember-me"
                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-bold" for="remember-me">Recordar mis
                                        datos</label>
                                </div>

                                <button type="submit" class="btn btn-dark w-100 py-2 fw-bold fs-5 shadow-sm">
                                    Entrar
                                </button>
                            </form>

                            <div class="text-center mt-4 pt-3 border-top border-light">
                                <p class="mb-2 small fw-bold">¿No tienes cuenta?</p>
                                <a href="{{ route('register') }}" class="btn btn-outline-dark w-100 btn-sm fw-bold">
                                    Crear cuenta nueva
                                </a>

                                <div class="mt-4">
                                    <a href="{{ route('shop.index') }}"
                                        class="text-decoration-none small text-muted fw-bold link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"
                                        data-bs-toggle="tooltip" title="Regresar al catálogo principal">
                                        <i class="bi bi-arrow-left"></i> Volver a la tienda
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <div class="accessibility-widget">
        <div id="access-menu" class="access-menu p-3" hidden>
            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                <h2 class="h6 fw-bold mb-0">Accesibilidad</h2>
                <button type="button" class="btn-close" aria-label="Cerrar menú"
                    onclick="document.getElementById('access-btn').click()"></button>
            </div>

            <div class="mb-3">
                <span class="d-block small fw-bold mb-2">Tamaño de texto</span>
                <div class="btn-group w-100" role="group" aria-label="Controles de tamaño de texto">
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-decrease-font"
                        aria-label="Disminuir letra">A-</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-reset-font"
                        aria-label="Letra normal">A</button>
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold" id="btn-increase-font"
                        aria-label="Aumentar letra">A+</button>
                </div>
            </div>

            <div class="mb-3">
                <button id="contrast-toggle-widget"
                    class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Alto Contraste</span>
                    <i class="bi bi-circle-half" aria-hidden="true"></i>
                </button>
            </div>

            <div class="mb-1">
                <button id="links-toggle"
                    class="btn btn-outline-dark w-100 btn-sm d-flex justify-content-between align-items-center fw-bold">
                    <span>Subrayar Enlaces</span>
                    <i class="bi bi-type-underline" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <button id="access-btn" class="btn btn-dark rounded-circle shadow-lg p-3 mt-2"
            aria-label="Abrir menú de accesibilidad" aria-expanded="false" aria-controls="access-menu"
            data-bs-toggle="tooltip" title="Opciones de Accesibilidad">
            <i class="bi bi-universal-access-circle fs-2"></i>
        </button>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('accessibility.js') }}"></script>

    <script>
        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const btn = document.querySelector('.toggle-password');

            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = 'Ocultar';
                btn.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                input.type = 'password';
                btn.textContent = 'Mostrar';
                btn.setAttribute('aria-label', 'Mostrar contraseña');
            }
        }
    </script>

</body>

</html>