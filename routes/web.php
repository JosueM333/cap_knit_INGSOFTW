<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;

// Rutas de autenticación (Login, Registro, Reset Password)
Auth::routes();

// ==========================================
// ZONA PÚBLICA (TIENDA)
// ==========================================

// Home Principal
Route::get('/', [HomeController::class, 'index'])->name('shop.index');

// Catálogo y Detalles
Route::get('/shop/productos', [HomeController::class, 'products'])
    ->name('shop.products');

Route::get('/shop/producto/{id}', [HomeController::class, 'show'])
    ->name('shop.show');

// Carrito y Contacto
Route::get('/shop/carrito', [HomeController::class, 'cart'])
    ->name('shop.cart');

Route::get('/shop/contacto', [HomeController::class, 'contact'])
    ->name('shop.contact');

// Lógica del Carrito
Route::get('/shop/add-to-cart/{id}', [HomeController::class, 'addToCart'])
    ->name('add.to.cart');

Route::patch('/shop/update-cart', [HomeController::class, 'updateCart'])
    ->name('update.cart');

Route::delete('/shop/remove-from-cart', [HomeController::class, 'removeCart'])
    ->name('remove.from.cart');


// ==========================================
// ZONA ADMIN (TEMPORAL - SIN SEGURIDAD)
// ==========================================

// 1. Dashboard (Panel Principal)
// NOTA: Se comentó el middleware('auth') para permitir acceso directo desde el botón temporal.
Route::get('/home', [HomeController::class, 'dashboard'])
    // ->middleware('auth')  // <--- COMENTADO PARA ENTRAR SIN LOGIN
    ->name('home');

// 2. Recursos (Tablas de gestión)
// NOTA: Se comentó el grupo middleware para poder editar/crear sin estar logueado.
// Route::middleware(['auth'])->group(function () { // <--- COMENTADO GRUPO DE SEGURIDAD

    Route::resource('clientes', ClienteController::class);
    Route::resource('bodegas', BodegaController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('carritos', CarritoController::class)->except(['show']);

// }); // <--- COMENTADO CIERRE DEL GRUPO