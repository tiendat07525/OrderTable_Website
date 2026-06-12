<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'phone' => '0987654321',
            'role' => 'customer',
        ]);

    }
}
