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
        Schema::create('TRANSACCION', function (Blueprint $table) {
            $table->id('TRA_ID'); // PK Autoincrement
            $table->string('TRA_CODIGO', 20)->unique(); // ENTRADA, SALIDA, AJUSTE
            $table->string('TRA_DESCRIPCION', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TRANSACCION');
    }
};
