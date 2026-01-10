<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {

        // Crear usuario
        $user = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Administrador',
                'apellidos' => 'Sistema',
                'identificacion' => '1234567890',
                'username' => 'admin',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password123'),
                'must_change_password' => false,
                'password_changed_at' => Carbon::now(),
            ]
        );

        // Asignar rol
        if (! $user->hasRole('Administrador')) {
            $user->assignRole('Administrador');
        }
    }
}

