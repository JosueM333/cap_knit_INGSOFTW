<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('BODEGA', function (Blueprint $table) {
            // BOD_ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY
            $table->id('BOD_ID');
            
            // BOD_NOMBRE VARCHAR2(80) NOT NULL
            $table->string('BOD_NOMBRE', 80);
            
            // BOD_UBICACION VARCHAR2(100) NOT NULL
            $table->string('BOD_UBICACION', 100);
            
            // BOD_DESCRIPCION VARCHAR2(200) (Es nullable según tu script que no tenía NOT NULL explícito)
            $table->string('BOD_DESCRIPCION', 200)->nullable();
            
            // BOD_ESTADO NUMBER(1) DEFAULT 1
            
            
            // Timestamps personalizados
            $table->timestamp('BOD_CREATED_AT')->useCurrent();
            $table->timestamp('BOD_UPDATED_AT')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('BODEGA');
    }
};