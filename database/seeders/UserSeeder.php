<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Kasir 1',
                'username' => 'admin',
                'email' => 'admin@kopikita.com',
                'password' => Hash::make('admin123'),
                'role' => 'kasir',
                'avatar' => null,
            ]
        );

        User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir Utama',
                'username' => 'kasir',
                'email' => 'kasir@kopikita.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'avatar' => null,
            ]
        );
    }
}
