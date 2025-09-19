<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // pastikan role admin sudah ada di Spatie
        Role::firstOrCreate([
            'name' => 'admin',
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // unik berdasarkan email
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin', // simpan ke kolom users.role
            ],
            
        );

        // assign role ke Spatie
        $admin->assignRole('admin');
    }
}
