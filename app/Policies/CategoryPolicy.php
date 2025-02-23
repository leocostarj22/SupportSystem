<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('view-categories');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('view-categories');
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('create-categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('edit-categories');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->role?->name === 'Super Admin' || $user->hasPermission('delete-categories');
    }
}
