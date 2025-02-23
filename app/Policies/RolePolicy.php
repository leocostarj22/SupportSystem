<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'Super Admin';
    }

    public function view(User $user, Role $role): bool
    {
        return $user->role?->name === 'Super Admin';
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Super Admin';
    }

    public function update(User $user, Role $role): bool
    {
        return $user->role?->name === 'Super Admin';
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->role?->name === 'Super Admin';
    }
}
