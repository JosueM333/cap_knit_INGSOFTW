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
        Schema::table('DETALLE_CARRITO', function (Blueprint $table) {
            $table->string('PRO_CODIGO', 20)->nullable()->after('PRO_ID');
            $table->string('PRO_NOMBRE', 100)->nullable()->after('PRO_CODIGO');
            // PRO_PRECIO/DCA_PRECIO_UNITARIO ya existe.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('DETALLE_CARRITO', function (Blueprint $table) {
            $table->dropColumn(['PRO_CODIGO', 'PRO_NOMBRE']);
        });
    }
};
