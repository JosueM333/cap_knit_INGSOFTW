<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión - Cap & Knit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; flex-direction: column; }
    </style>
</head>

<body class="bg-light">

<header class="bg-white border-bottom sticky-top py-3 shadow-sm mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        
        <a href="<?php echo e(url('/')); ?>" class="text-dark text-decoration-none fw-bold fs-4">
            CAP & KNIT
        </a>

        <nav class="d-none d-lg-block">
            <ul class="d-flex gap-4 list-unstyled text-uppercase small mb-0 align-items-center">
                <li><a href="<?php echo e(route('home')); ?>" class="text-decoration-none text-secondary fw-bold">Admin</a></li>
                <li><a href="<?php echo e(route('shop.index')); ?>" class="text-decoration-none text-secondary fw-bold">Tienda</a></li>
            </ul>
        </nav>

        <div class="d-flex align-items-center gap-3">
            <?php if(Auth::check() || Auth::guard('cliente')->check()): ?>
                <span class="fw-bold text-success">
                    <i class="bi bi-person-check-fill" aria-hidden="true"></i> 
                    <span class="d-none d-md-inline">Conectado</span>
                </span>
                
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 fw-bold" aria-label="Cerrar sesión">
                        Salir
                    </button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-dark fw-bold">Iniciar Sesión</a>
            <?php endif; ?>
        </div>

    </div>
</header>

<main class="container py-4 flex-grow-1">
    
    <?php if(session('success')): ?>
        <div class="alert alert-success border-success fw-bold" role="alert">
            <i class="bi bi-check-circle me-2" aria-hidden="true"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    
    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="bg-white border-top py-3 mt-auto text-center text-muted small">
    <div class="container">
        &copy; <?php echo e(date('Y')); ?> Cap & Knit - Panel de Gestión
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html><?php /**PATH C:\Users\USUARIO\Herd\cap-knit\resources\views/layouts/app.blade.php ENDPATH**/ ?>