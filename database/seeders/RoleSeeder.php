<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'description' => 'Full access to all features'
        ]);

        // Assign all permissions to Super Admin
        $superAdmin->permissions()->attach(Permission::all());

        // Create Manager
        $manager = Role::create([
            'name' => 'Manager',
            'description' => 'Manage day-to-day operations'
        ]);

        // Assign specific permissions to Manager
        $managerPermissions = Permission::whereIn('slug', [
            'view-users', 'view-tickets', 'create-tickets', 'edit-tickets', 
            'assign-tickets', 'view-categories'
        ])->get();
        
        $manager->permissions()->attach($managerPermissions);

        // Create Support
        $support = Role::create([
            'name' => 'Support',
            'description' => 'Handle tickets and basic operations'
        ]);

        // Assign specific permissions to Support
        $supportPermissions = Permission::whereIn('slug', [
            'view-tickets', 'create-tickets', 'edit-tickets', 'view-categories',
            'view-assigned-tickets', 'reply-tickets', 'update-ticket-status',
            'view-support-tickets', 'handle-assigned-tickets'
        ])->get();
        
        $support->permissions()->attach($supportPermissions);

        // Create Consumer
        $consumer = Role::create([
            'name' => 'Consumer',
            'description' => 'Regular user who can create and view their own tickets'
        ]);

        // Assign specific permissions to Consumer
        $consumerPermissions = Permission::whereIn('slug', [
            'view-tickets', 'create-tickets', 'view-own-tickets', 'reply-tickets'
        ])->get();
        
        $consumer->permissions()->attach($consumerPermissions);
    }
}