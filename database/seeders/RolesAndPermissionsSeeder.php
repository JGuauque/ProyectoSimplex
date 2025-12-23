<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. CREAR TODOS LOS PERMISOS QUE NECESITAS
        $permisos = [
            // Dashboard
            'ver dashboard',
            
            // Usuarios
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'resetear contraseña',
            
            // Clientes
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',
            
            // Inventario
            'ver inventario',
            'crear inventario',
            'editar inventario',
            'eliminar inventario',
            
            // Ventas
            'ver ventas',
            'crear ventas',
            'editar ventas',
            'eliminar ventas',
            'ver reportes ventas',
            
            // Préstamos
            'ver prestamos',
            'crear prestamos',
            'editar prestamos',
            'eliminar prestamos',
            
            // Turnos
            'ver turnos',
            'crear turnos',
            'editar turnos',
            'cerrar turnos',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 2. CREAR ROLES
        $roleOwner = Role::firstOrCreate(['name' => 'Owner']);
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $roleVendedor = Role::firstOrCreate(['name' => 'Vendedor']);

        // 3. ASIGNAR PERMISOS A CADA ROL
        
        // OWNER: Todos los permisos
        $roleOwner->syncPermissions([
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'resetear contraseña',
        ]);
        
        // ADMINISTRADOR: Casi todos, excepto quizás algunos sensibles
        $roleAdmin->syncPermissions(Permission::all());

        // VENDEDOR: Solo lo necesario para vender
        $roleVendedor->syncPermissions([
            // 'ver dashboard', // <-- NO TIENE ESTE PERMISO
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',

            'ver inventario',
            'crear inventario',
            'editar inventario',
            'eliminar inventario',

            'ver ventas',
            'crear ventas',
            'editar ventas',
            'eliminar ventas',
            'ver reportes ventas',

            'ver prestamos',
            'crear prestamos',
            'editar prestamos',
            'eliminar prestamos',

            'ver turnos',
            'editar turnos',
            'cerrar turnos',
        ]);
        

        // Asignar rol Owner al primer usuario (tu)
        
        $user = \App\Models\User::first();
        if ($user && !$user->hasRole('Owner')) {
            $user->assignRole('Owner');
        }

        // También puedes asignar roles a otros usuarios existentes
        // Ejemplo: segundo usuario como vendedor
        $secondUser = \App\Models\User::skip(1)->first();
        if ($secondUser && !$secondUser->hasAnyRole(['Owner', 'Administrador', 'Vendedor'])) {
            $secondUser->assignRole('Vendedor');
        }
    }
}
