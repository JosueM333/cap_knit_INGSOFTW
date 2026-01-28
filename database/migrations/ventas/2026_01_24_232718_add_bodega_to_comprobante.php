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
        Schema::connection('oracle_guayaquil')->table('COMPROBANTE', function (Blueprint $table) {
            $table->unsignedBigInteger('BOD_ID')->nullable()->after('CLI_ID');
            // Cross-DB: No foreign key
            // $table->foreign('BOD_ID')->references('BOD_ID')->on('BODEGA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('oracle_guayaquil')->table('COMPROBANTE', function (Blueprint $table) {
            // $table->dropForeign(['BOD_ID']);
            $table->dropColumn('BOD_ID');
        });
    }
};
