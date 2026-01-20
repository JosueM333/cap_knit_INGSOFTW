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
        Schema::create('CARRITO', function (Blueprint $table) {
            $table->id('CRD_ID'); // PK
            
            $table->foreignId('CLI_ID')->constrained('CLIENTE', 'CLI_ID')->onDelete('cascade');
            
            // 👇 ESTA ES LA LÍNEA QUE TE FALTABA PARA QUE FUNCIONE TU CÓDIGO 👇
            $table->dateTime('CRD_FECHA_CREACION')->nullable();

            $table->string('CRD_ESTADO')->default('ACTIVO');
            $table->decimal('CRD_SUBTOTAL', 10, 2)->default(0);
            $table->decimal('CRD_IMPUESTO', 10, 2)->default(0);
            $table->decimal('CRD_TOTAL', 10, 2)->default(0);
            
            // Puedes dejar timestamps si quieres tener created_at extra, 
            // pero CRD_FECHA_CREACION es la obligatoria para tu controlador.
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('CARRITO');
    }
};