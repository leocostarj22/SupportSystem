<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'group' => 'user'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'group' => 'user'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'group' => 'user'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'group' => 'user'],

            // Role Management
            ['name' => 'View Roles', 'slug' => 'view-roles', 'group' => 'role'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'group' => 'role'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'group' => 'role'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'group' => 'role'],

            // Permission Management
            ['name' => 'View Permissions', 'slug' => 'view-permissions', 'group' => 'permission'],
            ['name' => 'Create Permissions', 'slug' => 'create-permissions', 'group' => 'permission'],
            ['name' => 'Edit Permissions', 'slug' => 'edit-permissions', 'group' => 'permission'],
            ['name' => 'Delete Permissions', 'slug' => 'delete-permissions', 'group' => 'permission'],

            // Ticket Management
            ['name' => 'View Tickets', 'slug' => 'view-tickets', 'group' => 'ticket'],
            ['name' => 'Create Tickets', 'slug' => 'create-tickets', 'group' => 'ticket'],
            ['name' => 'Edit Tickets', 'slug' => 'edit-tickets', 'group' => 'ticket'],
            ['name' => 'Delete Tickets', 'slug' => 'delete-tickets', 'group' => 'ticket'],
            ['name' => 'Assign Tickets', 'slug' => 'assign-tickets', 'group' => 'ticket'],

            // Category Management
            ['name' => 'View Categories', 'slug' => 'view-categories', 'group' => 'category'],
            ['name' => 'Create Categories', 'slug' => 'create-categories', 'group' => 'category'],
            ['name' => 'Edit Categories', 'slug' => 'edit-categories', 'group' => 'category'],
            ['name' => 'Delete Categories', 'slug' => 'delete-categories', 'group' => 'category'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}