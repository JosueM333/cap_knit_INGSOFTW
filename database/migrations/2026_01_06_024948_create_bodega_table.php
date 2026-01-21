<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('BODEGA', function (Blueprint $table) {
            $table->id('BOD_ID'); // PK
            
            // Nombre único para evitar duplicados a nivel de BDD
            $table->string('BOD_NOMBRE', 80)->unique();
            
            $table->string('BOD_UBICACION', 100);
            $table->string('BOD_DESCRIPCION', 200)->nullable();
            
            // ELIMINADO: BOD_ESTADO (No necesario en borrado físico)
            
            $table->timestamps(); // created_at, updated_at estándar
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('BODEGA');
    }
};

