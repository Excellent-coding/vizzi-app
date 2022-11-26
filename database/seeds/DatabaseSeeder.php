<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => Hash::make('user'),
            'role' => 5
        ]);

        User::create([
            'name' => 'Client',
            'email' => 'client@mail.com',
            'password' => Hash::make('client'),
            'role' => 4
        ]);
        
        User::create([
            'name' => 'Manager',
            'email' => 'manager@mail.com',
            'password' => Hash::make('manager'),
            'role' => 3
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin'),
            'role' => 2
        ]);

        User::create([
            'name' => 'SuperAdmin',
            'email' => 'super@mail.com',
            'password' => Hash::make('superadmin'),
            'role' => 1
        ]);
    }
}
