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
        Schema::connection('oracle')->table('BODEGA', function (Blueprint $table) {
            $table->integer('BOD_ES_DEFECTO')->default(0)->after('BOD_DESCRIPCION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('oracle')->table('BODEGA', function (Blueprint $table) {
            $table->dropColumn('BOD_ES_DEFECTO');
        });
    }
};
