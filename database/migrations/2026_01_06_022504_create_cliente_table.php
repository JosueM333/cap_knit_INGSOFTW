<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Respetando el nombre de tabla 'CLIENTE' del script SQL
        Schema::create('CLIENTE', function (Blueprint $table) {
            // CLI_ID NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY
            $table->id('CLI_ID'); 
            
            // CLI_NOMBRES VARCHAR2(80) NOT NULL
            $table->string('CLI_NOMBRES', 80);
            
            // CLI_APELLIDOS VARCHAR2(80) NOT NULL
            $table->string('CLI_APELLIDOS', 80);
            
            // CLI_CEDULA VARCHAR2(10) NOT NULL UNIQUE
            $table->string('CLI_CEDULA', 10)->unique('IDX_CLIENTE_CEDULA');
            
            // CLI_EMAIL VARCHAR2(80) NOT NULL UNIQUE
            $table->string('CLI_EMAIL', 80)->unique('IDX_CLIENTE_EMAIL');
            
            // CLI_TELEFONO VARCHAR2(15) NOT NULL
            $table->string('CLI_TELEFONO', 15);
            
            // CLI_DIRECCION VARCHAR2(100) NOT NULL
            $table->string('CLI_DIRECCION', 100);
            
            // CLI_PASSWORD VARCHAR2(255) NOT NULL
            $table->string('CLI_PASSWORD', 255);
            
            // CLI_ESTADO NUMBER(1) DEFAULT 1
            $table->integer('CLI_ESTADO')->default(1);
            
            // Timestamps personalizados para coincidir con CLI_CREATED_AT y CLI_UPDATED_AT
            $table->timestamp('CLI_CREATED_AT')->useCurrent();
            $table->timestamp('CLI_UPDATED_AT')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CLIENTE');
    }
};