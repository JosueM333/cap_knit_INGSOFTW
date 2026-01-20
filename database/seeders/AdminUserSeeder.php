<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificamos si ya existe para no duplicarlo
        if (!User::where('email', 'admin@capandknit.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@capandknit.com', // Tu usuario "Root"
                'password' => Hash::make('admin123'), // Tu contraseña (cámbiala después)
                // 'email_verified_at' => now(), // Descomenta si requieres verificación
            ]);
            
            $this->command->info('¡Usuario Admin creado correctamente!');
        } else {
            $this->command->warn('El usuario Admin ya existe.');
        }
    }
}