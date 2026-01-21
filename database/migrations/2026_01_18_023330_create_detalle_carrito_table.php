<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('DETALLE_CARRITO', function (Blueprint $table) {
            // Usamos id() para consistencia con las otras tablas (equivale a bigIncrements)
            $table->id('DCA_ID'); 
            
            $table->foreignId('CRD_ID')->constrained('CARRITO', 'CRD_ID')->onDelete('cascade');
            $table->foreignId('PRO_ID')->constrained('PRODUCTO', 'PRO_ID')->onDelete('cascade');
            
            $table->integer('DCA_CANTIDAD');
            $table->decimal('DCA_PRECIO_UNITARIO', 10, 2);
            $table->decimal('DCA_SUBTOTAL', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DETALLE_CARRITO');
    }
};