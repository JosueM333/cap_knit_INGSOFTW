

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 text-uppercase">Gestión de Clientes</h1>
    <a href="<?php echo e(route('clientes.create')); ?>" class="btn btn-dark text-uppercase small">
        + Crear Cliente
    </a>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <form action="<?php echo e(route('clientes.index')); ?>" method="GET" class="row g-2">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" 
                       placeholder="Buscar por Cédula, Apellido o Email..." 
                       value="<?php echo e(request('search')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-dark w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cédula</th>
                    <th>Nombres</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    
                    <td><?php echo e($cli->cli_cedula); ?></td>
                    <td><?php echo e($cli->cli_apellidos); ?> <?php echo e($cli->cli_nombres); ?></td>
                    <td><?php echo e($cli->cli_email); ?></td>
                    <td><?php echo e($cli->cli_telefono); ?></td>
                    <td>
                        <?php if($cli->cli_estado === 'ACTIVO'): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        
                        <a href="<?php echo e(route('clientes.edit', ['id' => $cli->cli_id])); ?>" 
                           class="btn btn-sm btn-outline-primary me-1">
                           Editar
                        </a>
                        
                        <form action="<?php echo e(route('clientes.destroy', ['id' => $cli->cli_id])); ?>" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('¿Está seguro de eliminar este cliente?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No se encontraron clientes.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USUARIO\Herd\cap-knit\resources\views/clientes/index.blade.php ENDPATH**/ ?>