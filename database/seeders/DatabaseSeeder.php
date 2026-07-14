<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@carnival.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Promotor EO',
            'email' => 'promotor@carnival.com',
            'password' => Hash::make('password'),
            'role' => 'promotor'
        ]);

        User::create([
            'name' => 'Pembeli Biasa',
            'email' => 'buyer@carnival.com',
            'password' => Hash::make('password'),
            'role' => 'buyer'
        ]);
    }
}
