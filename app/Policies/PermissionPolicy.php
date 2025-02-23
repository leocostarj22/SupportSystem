<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('view-permissions');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('view-permissions');
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('create-permissions');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('edit-permissions');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('delete-permissions');
    }
}
