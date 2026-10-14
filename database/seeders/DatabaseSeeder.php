<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['name' => 'Admin',   'email' => 'admin@toko.test',   'role' => 'admin'],
            ['name' => 'Kasir',   'email' => 'kasir@toko.test',   'role' => 'kasir'],
            ['name' => 'Pemilik', 'email' => 'pemilik@toko.test', 'role' => 'pemilik'],
        ];

        foreach ($akun as $a) {
            User::updateOrCreate(
                ['email' => $a['email']],
                $a + ['password' => Hash::make('password')]
            );
        }
    }
}