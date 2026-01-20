<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cap & Knit</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos generales y alto contraste -->
    <link rel="stylesheet" href="{{ asset('styles.css') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .login-box {
            width: 100%;
            max-width: 360px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,.2);
            position: relative;
        }

        .field { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .password-wrapper { position: relative; }
        .toggle-password { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; background: none; border: none; font-size: 18px; }
        button[type="submit"], .btn-admin { width: 100%; padding: 10px; background: #333; color: #fff; border: none; cursor: pointer; border-radius: 4px; font-weight: 600; margin-top: 10px; }
        button[type="submit"]:hover, .btn-admin:hover { background: #000; }

        /* Botón de alto contraste */
        #contrast-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 6px 12px;
            border: 2px solid #6c757d;
            background: transparent;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        #contrast-toggle[aria-pressed="true"] { background: #000; color: #fff; border-color: #fff; }

        .is-invalid { border-color: #dc3545 !important; }
        .required::after { content: "*"; color: #dc3545; margin-left: 2px; }
    </style>
</head>
<body>

    <!-- Botón de alto contraste -->
    <button id="contrast-toggle" aria-pressed="false" aria-label="Activar modo de alto contraste">
        Alto contraste
    </button>

    <div class="login-box" role="form" aria-labelledby="login-title">
        <h2 id="login-title" class="text-center mb-4">Iniciar sesión</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Correo</label>
                <input id="email" type="email" name="email" required>
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <div class="password-wrapper">
                    <input id="password" type="password" name="password" required>
                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña">👁️</button>
                </div>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <!-- BOTÓN TEMPORAL PARA ABRIR MENU ADMIN -->
        <button class="btn-admin" onclick="window.location.href='{{ route('admin.menu') }}'">
            Abrir como Admin (temporal)
        </button>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS de accesibilidad -->
    <script src="{{ asset('accessibility.js') }}"></script>

    <!-- JS del ojo para mostrar/ocultar contraseña -->
    <script>
        const toggleBtn = document.querySelector(".toggle-password");
        const passwordInput = document.getElementById("password");

        toggleBtn.addEventListener("click", () => {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            toggleBtn.textContent = isPassword ? "🙈" : "👁️";
        });
    </script>

</body>
</html>
