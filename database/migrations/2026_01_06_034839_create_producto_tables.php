<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla PRODUCTO
        Schema::create('PRODUCTO', function (Blueprint $table) {
            $table->id('PRO_ID');
            
            // Relación con Proveedor
            $table->foreignId('PRV_ID')->constrained('PROVEEDOR', 'PRV_ID')->onDelete('cascade');
            
            $table->string('PRO_CODIGO', 20)->unique('IDX_PRODUCTO_CODIGO');
            $table->string('PRO_NOMBRE', 100);
            $table->string('PRO_DESCRIPCION', 500);
            $table->decimal('PRO_PRECIO', 10, 2);
            
            // Campos opcionales según tu script original
            $table->string('PRO_COLOR', 30)->nullable();
            $table->string('PRO_TALLA', 10)->nullable();
            $table->string('PRO_MARCA', 50)->nullable();
            
            $table->integer('PRO_ESTADO')->default(1); // 1=Activo
            $table->integer('PRO_VISIBLE')->default(1); // 1=Visible en web
            
            $table->timestamp('PRO_CREATED_AT')->useCurrent();
            $table->timestamp('PRO_UPDATED_AT')->useCurrent()->useCurrentOnUpdate();
        });

        // 2. Tabla BODEGA_PRODUCTO (Tabla Intermedia / Stock)
        Schema::create('BODEGA_PRODUCTO', function (Blueprint $table) {
            $table->foreignId('BOD_ID')->constrained('BODEGA', 'BOD_ID')->onDelete('cascade');
            $table->foreignId('PRO_ID')->constrained('PRODUCTO', 'PRO_ID')->onDelete('cascade');
            
            $table->integer('BP_STOCK')->default(0);     // Stock actual
            $table->integer('BP_STOCK_MIN')->default(5); // Alerta stock bajo
            
            $table->timestamp('BP_UPDATED_AT')->useCurrent()->useCurrentOnUpdate();
            
            // Llave primaria compuesta
            $table->primary(['BOD_ID', 'PRO_ID'], 'PK_BODEGA_PRODUCTO');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('BODEGA_PRODUCTO');
        Schema::dropIfExists('PRODUCTO');
    }
};