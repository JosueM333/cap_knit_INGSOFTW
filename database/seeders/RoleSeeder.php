<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos para POS
        $permissions = [
            'pos.carritos.view',
            'pos.carritos.manage',
            'pos.comprobantes.view',
            'pos.comprobantes.emit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Crear Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'ADMIN']);
        $roleCajero = Role::firstOrCreate(['name' => 'POS_CAJERO']);

        // 3. Asignar Permisos a Roles
        // Admin tiene todo (super-admin o asignar manualmente todos)
        $roleAdmin->givePermissionTo(Permission::all());

        // Cajero tiene solo permisos de POS
        $roleCajero->givePermissionTo($permissions);

        // 4. Crear Usuario Cajera (si no existe)
        $cajeroEmail = 'cajeraX@gmail.com';
        $userCajero = User::firstOrCreate(
            ['email' => $cajeroEmail],
            [
                'name' => 'Cajera X',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $userCajero->assignRole($roleCajero);

        // 5. Asignar Rol ADMIN al primer usuario (o a admin@gmail.com si existe)
        // Buscamos admin@gmail.com o el primer usuario si no existe
        $adminUser = User::where('email', 'admin@gmail.com')->first();
        if ($adminUser) {
            $adminUser->assignRole($roleAdmin);
        } else {
            // Fallback: Asignar al ID 1 si existe
            $firstUser = User::find(1);
            if ($firstUser) {
                $firstUser->assignRole($roleAdmin);
            }
        }

        $this->command->info('Roles y Permisos creados y asignados correctamente.');
    }
}
