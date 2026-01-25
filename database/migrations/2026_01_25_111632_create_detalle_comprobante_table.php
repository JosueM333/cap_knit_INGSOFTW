<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('DETALLE_COMPROBANTE', function (Blueprint $table) {
            $table->id('DCO_ID'); // PK

            // Relación con COMPROBANTE
            $table->unsignedBigInteger('COM_ID');
            $table->foreign('COM_ID')->references('COM_ID')->on('COMPROBANTE')->onDelete('cascade');

            // PRODUCTO (Sin FK, solo referencia)
            $table->unsignedBigInteger('PRO_ID');

            // Snapshots del Producto
            $table->string('PRO_CODIGO_SNAP', 20);
            $table->string('PRO_NOMBRE_SNAP', 100);

            // Valores monetarios y cantidades
            $table->integer('DCO_CANTIDAD');
            $table->decimal('DCO_PRECIO_UNITARIO', 10, 2);
            $table->decimal('DCO_SUBTOTAL', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('DETALLE_COMPROBANTE');
    }
};
