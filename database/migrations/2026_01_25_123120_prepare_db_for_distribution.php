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
        // 1. Eliminamos FK de DETALLE_CARRITO hacia PRODUCTO (BD2 -> BD1)
        Schema::table('DETALLE_CARRITO', function (Blueprint $table) {
            // Nombre de la constraint suele ser: tabla_columna_foreign
            // Intentamos borrarla por array sintaxis que laravel resuelve al nombre
            $table->dropForeign(['PRO_ID']);
        });

        // 2. Eliminamos FK de COMPROBANTE hacia BODEGA (BD2 -> BD1)
        Schema::table('COMPROBANTE', function (Blueprint $table) {
            $table->dropForeign(['BOD_ID']);
        });

        // 3. DETALLE_COMPROBANTE se creó sin FK a PRODUCTO, así que no es necesario acción.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restauramos FKs (Solo funcionará si bases están unidas)
        Schema::table('DETALLE_CARRITO', function (Blueprint $table) {
            $table->foreign('PRO_ID')->references('PRO_ID')->on('PRODUCTO')->onDelete('cascade');
        });

        Schema::table('COMPROBANTE', function (Blueprint $table) {
            $table->foreign('BOD_ID')->references('BOD_ID')->on('BODEGA');
        });
    }
};
