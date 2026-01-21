<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('DETALLE_CARRITO', function (Blueprint $table) {
            $table->increments('DCA_ID');
            $table->unsignedBigInteger('CRD_ID');
            $table->unsignedBigInteger('PRO_ID');
            $table->integer('DCA_CANTIDAD');
            $table->decimal('DCA_PRECIO_UNITARIO', 10, 2);
            $table->decimal('DCA_SUBTOTAL', 10, 2);

            $table->foreign('CRD_ID')
                  ->references('CRD_ID')
                  ->on('CARRITO');

            $table->foreign('PRO_ID')
                  ->references('PRO_ID')
                  ->on('PRODUCTO');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DETALLE_CARRITO');
    }
};

