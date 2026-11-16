<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@cuti.test',
            'no_wa' => '6281111111111',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'jatah_cuti_tahunan' => 12,
            'sisa_cuti_tahunan' => 12,
        ]);

        // Admin
        User::create([
            'name' => 'Admin HRD',
            'email' => 'admin@cuti.test',
            'no_wa' => '6282222222222',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'jatah_cuti_tahunan' => 12,
            'sisa_cuti_tahunan' => 12,
        ]);

        // Karyawan (buat 3 contoh)
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Karyawan {$i}",
                'email' => "karyawan{$i}@cuti.test",
                'no_wa' => '6285761929146' . $i,
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'jatah_cuti_tahunan' => 12,
                'sisa_cuti_tahunan' => 12,
            ]);
        }
    }
}
