<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('oracle_guayaquil')->create('CARRITO', function (Blueprint $table) {
            $table->id('CRD_ID');

            // Relación con Cliente
            $table->foreignId('CLI_ID')->constrained('CLIENTE', 'CLI_ID')->onDelete('cascade');

            // ELIMINADO: CRD_FECHA_CREACION (Usaremos created_at estándar)

            $table->string('CRD_ESTADO')->default('ACTIVO');

            // Totales
            $table->decimal('CRD_SUBTOTAL', 10, 2)->default(0);
            $table->decimal('CRD_IMPUESTO', 10, 2)->default(0);
            $table->decimal('CRD_TOTAL', 10, 2)->default(0);

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::connection('oracle_guayaquil')->dropIfExists('CARRITO');
    }
};