<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('oracle_guayaquil')->create('CLIENTE', function (Blueprint $table) {
            $table->id('CLI_ID'); // PK
            $table->string('CLI_NOMBRES');
            $table->string('CLI_APELLIDOS');
            $table->string('CLI_CEDULA')->unique();
            $table->string('CLI_EMAIL')->unique();
            $table->string('CLI_TELEFONO')->nullable();
            $table->string('CLI_DIRECCION')->nullable();
            $table->string('CLI_PASSWORD');

            // Estado definido como string, por defecto 'ACTIVO'
            $table->string('CLI_ESTADO')->default('ACTIVO');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('oracle_guayaquil')->dropIfExists('CLIENTE');
    }
};