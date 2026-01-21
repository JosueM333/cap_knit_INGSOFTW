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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas de autenticación
Auth::routes();

// ==================================================
// 🛒 ZONA PÚBLICA – CUALQUIERA PUEDE ENTRAR
// ==================================================

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

// Confirmar Compra
Route::post('/shop/comprar', [HomeController::class, 'comprar'])
    ->name('shop.comprar')
    ->middleware('auth:cliente,web'); 


// ==================================================
// 🔒 ZONA ADMIN (PROTEGIDA)
// ==================================================

Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Principal
    Route::get('/home', [HomeController::class, 'dashboard'])->name('home');

    // 2. CLIENTES
    // La clave aquí es parameters(['clientes' => 'id'])
    // Esto fuerza a la ruta a ser /clientes/{id}/edit en lugar de /clientes/{cliente}/edit
    Route::resource('clientes', ClienteController::class)->parameters([
        'clientes' => 'id'
    ]);

    // 3. Otros Recursos CRUD Básicos
    Route::resource('bodegas', BodegaController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ordenes', OrdenCompraController::class);

    // 4. Gestión de Carritos (Admin)
    Route::prefix('carritos')->group(function () {
        Route::get('/', [CarritoController::class, 'index'])->name('carritos.index');
        Route::get('/consultar', [CarritoController::class, 'consultar'])->name('carritos.consultar');
        
        // Búsquedas
        Route::post('/buscar-carrito', [CarritoController::class, 'buscarCarrito'])->name('carritos.buscar_carrito');
        Route::post('/buscar-cliente', [CarritoController::class, 'buscarCliente'])->name('carritos.buscar_cliente');
        Route::get('/buscar-cliente', function () { return redirect()->route('carritos.index'); });

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

    // 5. Gestión de Comprobantes (Admin)
    Route::prefix('comprobantes')->group(function () {
        Route::get('/', [ComprobanteController::class, 'index'])->name('comprobantes.index');
        Route::post('/buscar', [ComprobanteController::class, 'buscar'])->name('comprobantes.buscar');
        
        // Crear Factura
        Route::get('/crear', [ComprobanteController::class, 'create'])->name('comprobantes.create');
        Route::post('/', [ComprobanteController::class, 'store'])->name('comprobantes.store');
        
        // Editar y Ver
        Route::get('/{id}/editar', [ComprobanteController::class, 'edit'])->name('comprobantes.edit');
        Route::put('/{id}', [ComprobanteController::class, 'update'])->name('comprobantes.update');
        Route::get('/{id}', [ComprobanteController::class, 'show'])->name('comprobantes.show');
        
        // Anular
        Route::patch('/{id}/anular', [ComprobanteController::class, 'anular'])->name('comprobantes.anular');
    });

});