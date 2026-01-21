<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('CLIENTE', function (Blueprint $table) {
            $table->id('CLI_ID');
            $table->string('CLI_NOMBRES', 80);
            $table->string('CLI_APELLIDOS', 80);
            $table->string('CLI_CEDULA', 10)->unique();
            $table->string('CLI_EMAIL', 80)->unique();
            $table->string('CLI_TELEFONO', 15);
            $table->string('CLI_DIRECCION', 100);
            $table->string('CLI_PASSWORD');
            $table->string('CLI_ESTADO', 20)->default('ACTIVO'); // Necesario para borrado lógico
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CLIENTE');
    }
};