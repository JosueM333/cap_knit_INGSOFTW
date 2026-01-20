<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('COMPROBANTE', function (Blueprint $table) {
            // Clave Primaria
            $table->id('COM_ID');

            // Claves Foráneas
            // CRD_ID es unique para evitar doble facturación (Paso 10 / Excepción E3)
            $table->unsignedBigInteger('CRD_ID')->unique(); 
            $table->unsignedBigInteger('CLI_ID');

            // Datos del Comprobante
            $table->dateTime('COM_FECHA'); // Paso 7
            $table->decimal('COM_SUBTOTAL', 10, 2);
            $table->decimal('COM_IVA', 10, 2); // Para guardar el cálculo del 15%
            $table->decimal('COM_TOTAL', 10, 2);
            
            $table->string('COM_OBSERVACIONES', 255)->nullable(); // Paso 8
            $table->string('COM_ESTADO', 20)->default('EMITIDO');

            // Timestamps personalizados
            $table->timestamp('COM_CREATED_AT')->useCurrent();
            $table->timestamp('COM_UPDATED_AT')->useCurrent()->useCurrentOnUpdate();

            // Restricciones de Clave Foránea
            // Asumiendo que tus tablas se llaman CARRITO y CLIENTE
            $table->foreign('CRD_ID')->references('CRD_ID')->on('CARRITO');
            $table->foreign('CLI_ID')->references('CLI_ID')->on('CLIENTE');
        });
    }

    public function down()
    {
        Schema::dropIfExists('COMPROBANTE');
    }
};