<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla Cabecera (Orden de Compra)
        Schema::create('ORDEN_COMPRA', function (Blueprint $table) {
            $table->id('ORD_ID'); 
            
            // Relación con Proveedor
            $table->foreignId('PRV_ID')->constrained('PROVEEDOR', 'PRV_ID')->onDelete('cascade');

            $table->dateTime('ORD_FECHA');
            $table->decimal('ORD_TOTAL', 10, 2)->default(0);
            
            // Estado descriptivo
            $table->string('ORD_ESTADO')->default('PENDIENTE'); 

            $table->timestamps(); // created_at, updated_at
        });

        // 2. Tabla Detalle (Productos dentro de la orden)
        Schema::create('DETALLE_ORDEN', function (Blueprint $table) {
            $table->id('DOR_ID');

            // Relación con Cabecera
            $table->foreignId('ORD_ID')->constrained('ORDEN_COMPRA', 'ORD_ID')->onDelete('cascade');

            // Relación con Producto
            $table->foreignId('PRO_ID')->constrained('PRODUCTO', 'PRO_ID')->onDelete('cascade');

            $table->integer('DOR_CANTIDAD');
            $table->decimal('DOR_PRECIO', 10, 2);
            $table->decimal('DOR_SUBTOTAL', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DETALLE_ORDEN');
        Schema::dropIfExists('ORDEN_COMPRA');
    }
};