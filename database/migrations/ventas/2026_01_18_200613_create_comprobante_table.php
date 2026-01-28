<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('oracle_guayaquil')->create('COMPROBANTE', function (Blueprint $table) {
            // Clave Primaria
            $table->id('COM_ID');

            // Claves Foráneas
            // CRD_ID es unique para asegurar una sola factura por carrito
            $table->foreignId('CRD_ID')->unique()->constrained('CARRITO', 'CRD_ID');
            $table->foreignId('CLI_ID')->constrained('CLIENTE', 'CLI_ID');

            // Datos del Comprobante
            $table->dateTime('COM_FECHA');
            $table->decimal('COM_SUBTOTAL', 10, 2);
            $table->decimal('COM_IVA', 10, 2);
            $table->decimal('COM_TOTAL', 10, 2);

            // Observaciones y Estado
            $table->string('COM_OBSERVACIONES', 255)->nullable();
            $table->string('COM_ESTADO', 20)->default('EMITIDO');

            // Timestamps estándar (created_at, updated_at)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('oracle_guayaquil')->dropIfExists('COMPROBANTE');
    }
};