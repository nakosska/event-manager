<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@eventmanager.com',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Organizer',
                'email' => 'organizer@eventmanager.com',
                'password' => Hash::make('12345678'),
                'role' => 'organizer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Participant',
                'email' => 'user@eventmanager.com',
                'password' => Hash::make('12345678'),
                'role' => 'participant',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
