<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\OrdenCompraController; 
use App\Http\Controllers\ComprobanteController;

// Rutas de autenticación (Login, Logout, etc.)
Auth::routes();

// ==================================================
// 🛒 ZONA PÚBLICA – CUALQUIERA PUEDE ENTRAR
// ==================================================

Route::get('/', [HomeController::class, 'index'])->name('shop.index');

Route::get('/shop/productos', [HomeController::class, 'products'])->name('shop.products');
Route::get('/shop/producto/{id}', [HomeController::class, 'show'])->name('shop.show');
Route::get('/shop/carrito', [HomeController::class, 'cart'])->name('shop.cart');
Route::get('/shop/contacto', [HomeController::class, 'contact'])->name('shop.contact');

// --- Carrito en sesión (Cliente) ---
Route::get('/shop/add-to-cart/{id}', [HomeController::class, 'addToCart'])->name('add.to.cart');
Route::patch('/shop/update-cart', [HomeController::class, 'updateCart'])->name('update.cart');
Route::delete('/shop/remove-from-cart', [HomeController::class, 'removeCart'])->name('remove.from.cart');

// --- Confirmar Compra ---
// CAMBIO IMPORTANTE: Aceptamos 'cliente' (para compradores reales) y 'web' (para que tú pruebes)
Route::post('/shop/comprar', [HomeController::class, 'comprar'])
    ->name('shop.comprar')
    ->middleware('auth:cliente,web'); 


// ==================================================
// 🔒 ZONA ADMIN (PROTEGIDA)
// Solo accesible si estás logueado como Admin (users table)
// ==================================================

// 'auth' por defecto busca en el guard 'web' (Admin), así que esto protege todo el grupo
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Principal
    Route::get('/home', [HomeController::class, 'dashboard'])->name('home');

    // 2. Recursos CRUD Básicos
    Route::resource('clientes', ClienteController::class);
    Route::resource('bodegas', BodegaController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ordenes', OrdenCompraController::class);

    // 3. Gestión de Carritos (Admin)
    Route::prefix('carritos')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('carritos.index');
        Route::get('/consultar', [CarritoController::class, 'consultar'])->name('carritos.consultar');
        Route::post('/buscar-carrito', [CarritoController::class, 'buscarCarrito'])->name('carritos.buscar_carrito');
        Route::post('/buscar-cliente', [CarritoController::class, 'buscarCliente'])->name('carritos.buscar_cliente');
        
        // Redirección por seguridad
        Route::get('/buscar-cliente', function () { return redirect()->route('carritos.index'); });

        // Rutas con ID
        Route::get('/cliente/{id}', [CarritoController::class, 'seleccionarCliente'])->name('carritos.seleccionar_cliente');
        Route::get('/{id}/editar', [CarritoController::class, 'editar'])->name('carritos.editar');
        Route::patch('/detalle/{id}', [CarritoController::class, 'actualizarDetalle'])->name('carritos.actualizar_detalle');
        Route::delete('/detalle/{id}', [CarritoController::class, 'eliminarDetalle'])->name('carritos.eliminar_detalle');
        Route::delete('/{id}/vaciar', [CarritoController::class, 'vaciar'])->name('carritos.vaciar');
        Route::post('/{id}/guardar', [CarritoController::class, 'guardar'])->name('carritos.guardar');
        Route::post('/{id}/buscar-producto', [CarritoController::class, 'buscarProducto'])->name('carritos.buscar_producto');
        Route::post('/{id}/agregar-producto', [CarritoController::class, 'agregarProducto'])->name('carritos.agregar_producto');
    });

    // 4. Gestión de Comprobantes (Admin)
    Route::prefix('comprobantes')->group(function () {
        Route::get('/', [ComprobanteController::class, 'index'])->name('comprobantes.index');
        Route::post('/buscar', [ComprobanteController::class, 'buscar'])->name('comprobantes.buscar');
        Route::get('/crear', [ComprobanteController::class, 'create'])->name('comprobantes.create');
        Route::post('/', [ComprobanteController::class, 'store'])->name('comprobantes.store');
        Route::get('/{id}/editar', [ComprobanteController::class, 'edit'])->name('comprobantes.edit');
        Route::put('/{id}', [ComprobanteController::class, 'update'])->name('comprobantes.update');
        Route::patch('/{id}/anular', [ComprobanteController::class, 'anular'])->name('comprobantes.anular');
        Route::get('/{id}', [ComprobanteController::class, 'show'])->name('comprobantes.show');
    });

}); // Fin del grupo Admin