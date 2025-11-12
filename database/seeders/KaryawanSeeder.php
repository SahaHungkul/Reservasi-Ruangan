<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // Pastikan role 'karyawan' tersedia
        $karyawanRole = Role::firstOrCreate(['name' => 'karyawan']);

        // Buat 10 akun dummy karyawan
        for ($i = 1; $i <= 10; $i++) {
            $email = "karyawan{$i}@example.com";

            $user = User::firstOrCreate(
                ['email' => $email], // cek berdasarkan email
                [
                    'name' => "Karyawan {$i}",
                    'password' => Hash::make('password123'), // password default
                    'role' => 'karyawan', // simpan juga di kolom users.role
                ]
            );

            // Assign role Spatie jika belum ada
            if (! $user->hasRole('karyawan')) {
                $user->assignRole($karyawanRole);
            }
        }

        $this->command->info('✅ 10 akun dummy karyawan berhasil dibuat (email: karyawan1@example.com - karyawan10@example.com, password: password123)');
    }
}
