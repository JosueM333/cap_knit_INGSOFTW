<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PROVEEDOR', function (Blueprint $table) {
            // PRV_ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY
            $table->id('PRV_ID');
            
            // PRV_RUC VARCHAR2(13) NOT NULL UNIQUE
            $table->string('PRV_RUC', 13)->unique('IDX_PROVEEDOR_RUC');
            
            // PRV_NOMBRE VARCHAR2(100) NOT NULL
            $table->string('PRV_NOMBRE', 100);
            
            // PRV_TELEFONO VARCHAR2(15) NOT NULL
            $table->string('PRV_TELEFONO', 15);
            
            // PRV_EMAIL VARCHAR2(80) NOT NULL
            $table->string('PRV_EMAIL', 80);
            
            // PRV_DIRECCION VARCHAR2(100) NOT NULL
            $table->string('PRV_DIRECCION', 100);
            
            // PRV_PERSONA_CONTACTO VARCHAR2(80) (Nullable según script original, aunque diagrama pide datos completos)
            // Lo pondré nullable para flexibilidad, pero validaremos requeridos en el modelo según diagrama F1.1
            $table->string('PRV_PERSONA_CONTACTO', 80)->nullable();
            
            // PRV_ESTADO NUMBER(1) DEFAULT 1
            
            
            // Timestamps
            $table->timestamp('PRV_CREATED_AT')->useCurrent();
            $table->timestamp('PRV_UPDATED_AT')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PROVEEDOR');
    }
};