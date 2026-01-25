<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Importación de Controladores
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\KardexController;

/*
|--------------------------------------------------------------------------
| RUTA DE BYPASS TEMPORAL - ELIMINAR EN PRODUCCIÓN
|--------------------------------------------------------------------------
| Accede a /test-admin para auto-loguearte y probar Oracle sin login
*/
Route::get('/test-admin', function () {
    $user = \App\Models\User::first();
    if ($user) {
        Auth::login($user);
        return redirect()->route('home')->with('success', '¡Bypass exitoso! Estás logueado como admin.');
    }
    return redirect('/')->with('error', 'No hay usuarios. Ejecuta: php artisan db:seed --class=AdminUserSeeder');
});

Auth::routes();

// Rutas Públicas - Accesibles para todos los usuarios y no gatitos
Route::get('/', [HomeController::class, 'index'])->name('shop.index');

// Catálogo y Productos
Route::get('/shop/productos', [HomeController::class, 'products'])->name('shop.products');
Route::get('/shop/producto/{id}', [HomeController::class, 'show'])->name('shop.show');
Route::get('/shop/contacto', [HomeController::class, 'contact'])->name('shop.contact');

// Carrito de Compras
Route::get('/shop/carrito', [HomeController::class, 'cart'])->name('shop.cart');
Route::get('/shop/add-to-cart/{id}', [HomeController::class, 'addToCart'])->name('add.to.cart');
Route::patch('/shop/update-cart', [HomeController::class, 'updateCart'])->name('update.cart');
Route::delete('/shop/remove-from-cart', [HomeController::class, 'removeCart'])->name('remove.from.cart');

// Confirmar Compra (Permite clientes reales y admins probando)
Route::post('/shop/comprar', [HomeController::class, 'comprar'])
    ->name('shop.comprar')
    ->middleware('auth:cliente,web');


// ZONA DE ADMIN – SOLO USUARIOS AUTENTICADOS Y GATITOS PUEDEN ENTRAR

Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Principal
    Route::get('/home', [HomeController::class, 'dashboard'])->name('home');

    // 2. Recursos CRUD Básicos
    Route::resource('clientes', ClienteController::class);
    Route::resource('bodegas', BodegaController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ordenes', OrdenCompraController::class);
    // Ruta personalizada para recibir orden
    Route::post('/ordenes/{id}/recibir', [OrdenCompraController::class, 'recibirOrden'])->name('ordenes.recibir');

    // 3. Gestión de Carritos (Admin)
    Route::prefix('carritos')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('carritos.index');
        //Route::get('/consultar', [CarritoController::class, 'consultar'])->name('cansultar');

        // Búsquedas
        Route::post('/buscar-carrito', [CarritoController::class, 'buscarCarrito'])->name('carritos.buscar_carrito');
        Route::post('/buscar-cliente', [CarritoController::class, 'buscarCliente'])->name('carritos.buscar_cliente');
        // Redirección de seguridad para GET en buscar
        Route::get('/buscar-cliente', function () {
            return redirect()->route('carritos.index');
        });

        // Operaciones sobre carritos específicos
        Route::get('/cliente/{id}', [CarritoController::class, 'seleccionarCliente'])->name('carritos.seleccionar_cliente');
        Route::get('/{id}/editar', [CarritoController::class, 'editar'])->name('carritos.editar');
        Route::patch('/detalle/{id}', [CarritoController::class, 'actualizarDetalle'])->name('carritos.actualizar_detalle');
        Route::delete('/detalle/{id}', [CarritoController::class, 'eliminarDetalle'])->name('carritos.eliminar_detalle');
        Route::delete('/{id}/vaciar', [CarritoController::class, 'vaciar'])->name('carritos.vaciar');

        // Guardar cambios y agregar productos manuales
        Route::post('/{id}/guardar', [CarritoController::class, 'guardar'])->name('carritos.guardar');
        Route::post('/{id}/buscar-producto', [CarritoController::class, 'buscarProducto'])->name('carritos.buscar_producto');
        Route::post('/{id}/agregar-producto', [CarritoController::class, 'agregarProducto'])->name('carritos.agregar_producto');
    });

    // 4. Gestión de Comprobantes (Admin)
    Route::prefix('comprobantes')->group(function () {
        // Listado y Búsqueda
        Route::get('/', [ComprobanteController::class, 'index'])->name('comprobantes.index');
        Route::post('/buscar', [ComprobanteController::class, 'buscar'])->name('comprobantes.buscar');

        // Crear Factura (Emitir)
        Route::get('/crear', [ComprobanteController::class, 'create'])->name('comprobantes.create');
        Route::post('/', [ComprobanteController::class, 'store'])->name('comprobantes.store'); // <-- Esta usa el Modal de Crear

        // Editar y Ver
        Route::get('/{id}/editar', [ComprobanteController::class, 'edit'])->name('comprobantes.edit');
        Route::put('/{id}', [ComprobanteController::class, 'update'])->name('comprobantes.update');
        Route::get('/{id}', [ComprobanteController::class, 'show'])->name('comprobantes.show');

        // Anular (Borrado Lógico)
        // IMPORTANTE: Esta es la que usa el Modal de Anulación con @method('PATCH')
        Route::patch('/{id}/anular', [ComprobanteController::class, 'anular'])->name('comprobantes.anular');
    });

    // 5. Reporte Kardex
    Route::get('/kardex', [KardexController::class, 'index'])->name('kardex.index');

}); // Fin del grupo Admin