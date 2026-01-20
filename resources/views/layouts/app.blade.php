<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    </style>
</head>

<body class="bg-light">

<header class="bg-white border-bottom sticky-top py-3 shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        
        <a href="{{ url('/') }}" class="text-dark text-decoration-none fw-bold fs-4">
            CAP & KNIT
        </a>

        <nav class="d-none d-lg-block">
            <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center">
                <li><a href="{{ url('/home') }}" class="text-decoration-none text-secondary">Admin</a></li>
                <li><a href="{{ url('/') }}" class="text-decoration-none text-secondary">Tienda</a></li>
            </ul>
        </nav>

        <div class="d-flex align-items-center gap-3">
            @if(Auth::check() || Auth::guard('cliente')->check())
                <span class="fw-bold"><i class="bi bi-person-check-fill"></i> Conectado</span>
                
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 fw-bold">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-dark">Iniciar Sesión</a>
            @endif
        </div>

    </div>
</header>

<main class="container py-4 flex-grow-1">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @yield('content')
</main>

<script src="