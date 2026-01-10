<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\HomeController; // Importamos el Home

// Ruta Principal (Menú Principal)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de los Módulos
Route::resource('clientes', ClienteController::class);
Route::resource('bodegas', BodegaController::class);
Route::resource('proveedores', ProveedorController::class);
Route::resource('productos', ProductoController::class);