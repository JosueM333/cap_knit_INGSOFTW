<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tabla PRODUCTO
        Schema::connection('oracle')->create('PRODUCTO', function (Blueprint $table) {
            $table->id('PRO_ID');

            // Relación con Proveedor (Borrado en cascada si se borra el proveedor)
            $table->foreignId('PRV_ID')->constrained('PROVEEDOR', 'PRV_ID')->onDelete('cascade');

            $table->string('PRO_CODIGO', 20)->unique();
            $table->string('PRO_NOMBRE', 100);
            $table->string('PRO_DESCRIPCION', 500);
            $table->decimal('PRO_PRECIO', 10, 2);

            // Atributos opcionales
            $table->string('PRO_COLOR', 30)->nullable();
            $table->string('PRO_TALLA', 10)->nullable();
            $table->string('PRO_MARCA', 50)->nullable();



            $table->timestamps(); // created_at, updated_at
        });

        // 2. Tabla BODEGA_PRODUCTO (Stock)
        Schema::connection('oracle')->create('BODEGA_PRODUCTO', function (Blueprint $table) {
            $table->foreignId('BOD_ID')->constrained('BODEGA', 'BOD_ID')->onDelete('cascade');
            $table->foreignId('PRO_ID')->constrained('PRODUCTO', 'PRO_ID')->onDelete('cascade');

            $table->integer('BP_STOCK')->default(0);
            $table->integer('BP_STOCK_MIN')->default(5);

            $table->timestamps(); // Standard timestamps para la pivote

            $table->primary(['BOD_ID', 'PRO_ID']);
        });
    }

    public function down(): void
    {
        Schema::connection('oracle')->dropIfExists('BODEGA_PRODUCTO');
        Schema::connection('oracle')->dropIfExists('PRODUCTO');
    }
};