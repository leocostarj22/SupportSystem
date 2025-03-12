<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles first
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $manager = Role::create(['name' => 'Manager']);
        $support = Role::create(['name' => 'Support']);

        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => $superAdmin->id,
            'is_active' => true,
        ]);

        // Create Manager
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role_id' => $manager->id,
            'is_active' => true,
        ]);

        // Create Support User
        User::create([
            'name' => 'Support User',
            'email' => 'support@example.com',
            'password' => Hash::make('password'),
            'role_id' => $support->id,
            'is_active' => true,
        ]);
    }
}