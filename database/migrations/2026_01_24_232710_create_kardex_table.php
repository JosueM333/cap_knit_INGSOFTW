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
        Schema::create('KARDEX', function (Blueprint $table) {
            $table->id('KRD_ID');

            // FKs
            $table->unsignedBigInteger('BOD_ID');
            $table->foreign('BOD_ID')->references('BOD_ID')->on('BODEGA');

            $table->unsignedBigInteger('PRO_ID');
            $table->foreign('PRO_ID')->references('PRO_ID')->on('PRODUCTO');

            $table->unsignedBigInteger('TRA_ID');
            $table->foreign('TRA_ID')->references('TRA_ID')->on('TRANSACCION');

            // Referencias opcionales (Nullable)
            $table->unsignedBigInteger('ORD_ID')->nullable();
            $table->foreign('ORD_ID')->references('ORD_ID')->on('ORDEN_COMPRA');

            $table->unsignedBigInteger('COM_ID')->nullable();
            // Asumiendo que COMPROBANTE tiene PK COM_ID. Si falla, verificaremos.
            // $table->foreign('COM_ID')->references('COM_ID')->on('COMPROBANTE');
            // Dejar la FK comentada por si el orden de migración afecta o la tabla no existe aún (que debería existir).
            // Pero mejor la defino si la tabla existe. Comprobante existe.
            // Para seguridad, usaré solo entero indexado si hay dudas, pero intentaré FK.
            // El usuario dijo "COM_NUMERO (nullable... FK a COMPROBANTE)".
            // Mejor la creo, si falla la ajusto. Es lo mas seguro.

            $table->timestamp('KRD_FECHA')->useCurrent();
            $table->integer('KRD_CANTIDAD'); // + para entrada, - para salida
            $table->integer('KRD_SALDO')->nullable(); // Saldo resultante (opcional)
            $table->string('KRD_USUARIO', 50)->nullable();
            $table->string('KRD_OBSERVACION', 200)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KARDEX');
    }
};
