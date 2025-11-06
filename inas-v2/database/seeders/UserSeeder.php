<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;                 // <-- Import Model User
use Illuminate\Support\Facades\Hash; // <-- Import Hash sesuai permintaan Anda
use Illuminate\Support\Str;          // <-- Import Str untuk remember_token

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat satu user spesifik secara manual
        //    (Seperti yang Anda minta, menggunakan Hash)
        User::create([
            'name' => 'Admin INAS',
            'email' => 'admin@inas.click',
            'email_verified_at' => now(),
            'password' => Hash::make('admininas124$'), // <-- Ganti 'password123' dengan password Anda
            'remember_token' => Str::random(10),
        ]);

        // 2. Membuat 10 user dummy tambahan menggunakan factory
        //    Ini cara cepat untuk mengisi data acak
        User::factory(10)->create();
    }
}