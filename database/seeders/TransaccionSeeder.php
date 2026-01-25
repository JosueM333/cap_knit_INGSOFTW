<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transacciones = [
            ['TRA_CODIGO' => 'ENTRADA', 'TRA_DESCRIPCION' => 'Ingreso por compra / recepción', 'created_at' => now(), 'updated_at' => now()],
            ['TRA_CODIGO' => 'SALIDA', 'TRA_DESCRIPCION' => 'Egreso por venta / facturación', 'created_at' => now(), 'updated_at' => now()],
            ['TRA_CODIGO' => 'AJUSTE', 'TRA_DESCRIPCION' => 'Ajuste manual / corrección', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($transacciones as $transaccion) {
            DB::table('TRANSACCION')->updateOrInsert(
                ['TRA_CODIGO' => $transaccion['TRA_CODIGO']],
                $transaccion
            );
        }
    }
}
