<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('oracle')->create('PROVEEDOR', function (Blueprint $table) {
            $table->id('PRV_ID');
            $table->string('PRV_RUC', 13)->unique();
            $table->string('PRV_NOMBRE', 100);
            $table->string('PRV_TELEFONO', 15);
            $table->string('PRV_EMAIL', 80);
            $table->string('PRV_DIRECCION', 100);
            $table->string('PRV_PERSONA_CONTACTO', 80)->nullable();

            // ELIMINADO: PRV_ESTADO (No es necesario en borrado físico)

            $table->timestamps(); // created_at, updated_at estándar
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PROVEEDOR');
    }
};