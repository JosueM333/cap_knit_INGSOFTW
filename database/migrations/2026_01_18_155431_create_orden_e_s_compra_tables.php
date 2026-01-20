<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla Cabecera (Orden de Compra)
        Schema::create('ORDEN_COMPRA', function (Blueprint $table) {
            $table->id('ORD_ID'); // Identificador
            
            // Relación con Proveedor (Clave Foránea para integridad E3)
            $table->unsignedBigInteger('PRV_ID');
            $table->foreign('PRV_ID')->references('PRV_ID')->on('PROVEEDOR');

            $table->dateTime('ORD_FECHA');
            $table->decimal('ORD_TOTAL', 10, 2)->default(0);
            $table->char('ORD_ESTADO', 1)->default('A'); // A: Activa/Pendiente

            // Timestamps personalizados
            $table->timestamp('ORD_CREATED_AT')->nullable();
            $table->timestamp('ORD_UPDATED_AT')->nullable();
        });

        // Tabla Detalle (Productos dentro de la orden)
        Schema::create('DETALLE_ORDEN', function (Blueprint $table) {
            $table->id('DOR_ID');

            // Relación con la Cabecera (Si borras la orden, se borran sus detalles)
            $table->unsignedBigInteger('ORD_ID');
            $table->foreign('ORD_ID')->references('ORD_ID')->on('ORDEN_COMPRA')->onDelete('cascade');

            // Relación con Producto
            $table->unsignedBigInteger('PRO_ID');
            $table->foreign('PRO_ID')->references('PRO_ID')->on('PRODUCTO');

            $table->integer('DOR_CANTIDAD');
            $table->decimal('DOR_PRECIO', 10, 2); // Precio pactado en ese momento
            $table->decimal('DOR_SUBTOTAL', 10, 2);

            $table->timestamp('DOR_CREATED_AT')->nullable();
            $table->timestamp('DOR_UPDATED_AT')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DETALLE_ORDEN');
        Schema::dropIfExists('ORDEN_COMPRA');
    }
};