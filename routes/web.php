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

Auth::routes();

// ==================================================
// ZONA PÚBLICA – CLIENTE (SHOP)
// ==================================================

Route::get('/', [HomeController::class, 'index'])->name('shop.index');

Route::get('/shop/productos', [HomeController::class, 'products'])
    ->name('shop.products');

Route::get('/shop/producto/{id}', [HomeController::class, 'show'])
    ->name('shop.show');

Route::get('/shop/carrito', [HomeController::class, 'cart'])
    ->name('shop.cart');

Route::get('/shop/contacto', [HomeController::class, 'contact'])
    ->name('shop.contact');

// --- carrito en sesión (cliente) ---
Route::get('/shop/add-to-cart/{id}', [HomeController::class, 'addToCart'])
    ->name('add.to.cart');

Route::patch('/shop/update-cart', [HomeController::class, 'updateCart'])
    ->name('update.cart');

Route::delete('/shop/remove-from-cart', [HomeController::class, 'removeCart'])
    ->name('remove.from.cart');

// ==================================================
// 🔑 NUEVA RUTA – CONFIRMAR COMPRA (PUENTE CLIENTE → ADMIN)
// ==================================================

Route::post('/shop/comprar', [HomeController::class, 'comprar'])
    ->name('shop.comprar')
    ->middleware('auth');

// ==================================================
// ZONA ADMIN
// ==================================================

Route::get('/home', [HomeController::class, 'dashboard'])
    ->name('home');

Route::resource('clientes', ClienteController::class);
Route::resource('bodegas', BodegaController::class);
Route::resource('proveedores', ProveedorController::class);
Route::resource('productos', ProductoController::class);

// 2. AGREGAMOS LA RUTA RESOURCE PARA ÓRDENES DE COMPRA
Route::resource('ordenes', OrdenCompraController::class);


/*
|--------------------------------------------------------------------------
| CARRITOS – GESTIÓN COMPLETA (ADMIN) F7.1 a F7.5
|--------------------------------------------------------------------------
*/

// F7.1 / F7.2 – Pantalla principal
Route::get('/carritos', [CarritoController::class, 'index'])
    ->name('carritos.index');

// F7.2 – Consultar todos
Route::get('/carritos/consultar', [CarritoController::class, 'consultar'])
    ->name('carritos.consultar');

// F7.3 – Buscar carrito por cliente
Route::post('/carritos/buscar-carrito', [CarritoController::class, 'buscarCarrito'])
    ->name('carritos.buscar_carrito');

// F7.1 – Buscar cliente
Route::post('/carritos/buscar-cliente', [CarritoController::class, 'buscarCliente'])
    ->name('carritos.buscar_cliente');

Route::get('/carritos/buscar-cliente', function () {
    return redirect()->route('carritos.index');
});

// --- RUTAS CON ID (AL FINAL) ---

// F7.1 – Seleccionar cliente
Route::get('/carritos/cliente/{id}', [CarritoController::class, 'seleccionarCliente'])
    ->name('carritos.seleccionar_cliente');

// F7.4 – Editar carrito
Route::get('/carritos/{id}/editar', [CarritoController::class, 'editar'])
    ->name('carritos.editar');

// F7.4 – Actualizar cantidad
Route::patch('/carritos/detalle/{id}', [CarritoController::class, 'actualizarDetalle'])
    ->name('carritos.actualizar_detalle');

// F7.4 – Eliminar detalle
Route::delete('/carritos/detalle/{id}', [CarritoController::class, 'eliminarDetalle'])
    ->name('carritos.eliminar_detalle');

// F7.5 – Vaciar carrito
Route::delete('/carritos/{id}/vaciar', [CarritoController::class, 'vaciar'])
    ->name('carritos.vaciar');

// F7.4 – Guardar carrito
Route::post('/carritos/{id}/guardar', [CarritoController::class, 'guardar'])
    ->name('carritos.guardar');

// F7.4 – Buscar / agregar producto
Route::post('/carritos/{id}/buscar-producto', [CarritoController::class, 'buscarProducto'])
    ->name('carritos.buscar_producto');

Route::post('/carritos/{id}/agregar-producto', [CarritoController::class, 'agregarProducto'])
    ->name('carritos.agregar_producto');


/*
|--------------------------------------------------------------------------
| COMPROBANTES – GESTIÓN COMPLETA (F5.1 a F5.5)
|--------------------------------------------------------------------------
*/

// F5.2: Pantalla Principal (Listado)
Route::get('/comprobantes', [ComprobanteController::class, 'index'])
    ->name('comprobantes.index');

// F5.3: Buscar (Acción del botón "Buscar")
Route::post('/comprobantes/buscar', [ComprobanteController::class, 'buscar'])
    ->name('comprobantes.buscar');

// F5.1: Crear - Pantalla de selección de ventas pendientes
Route::get('/comprobantes/crear', [ComprobanteController::class, 'create'])
    ->name('comprobantes.create');

// F5.1: Guardar y emitir
Route::post('/comprobantes', [ComprobanteController::class, 'store'])
    ->name('comprobantes.store');

// F5.4: Editar (Formulario - Solo observaciones)
Route::get('/comprobantes/{id}/editar', [ComprobanteController::class, 'edit'])
    ->name('comprobantes.edit');

// F5.4: Actualizar (Acción PUT)
Route::put('/comprobantes/{id}', [ComprobanteController::class, 'update'])
    ->name('comprobantes.update');

// F5.5: Anular (Acción PATCH - Borrado Lógico)
Route::patch('/comprobantes/{id}/anular', [ComprobanteController::class, 'anular'])
    ->name('comprobantes.anular');

// F5.1 / F5.2: Ver documento generado (SIEMPRE AL FINAL DEL GRUPO)
Route::get('/comprobantes/{id}', [ComprobanteController::class, 'show'])
    ->name('comprobantes.show');