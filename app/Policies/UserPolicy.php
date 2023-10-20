<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function storeEmployee(User $user): bool
    {
        return $user->hasRole('manager');
    }

    public function storePost(User $user): bool
    {
        return $user->hasRole('employee');
    }

    public function updatePost(User $user): bool
    {
        return $user->hasRole('employee');
    }

    public function readPost(User $user): bool
    {
        $allowedRoles = ['employee', 'manager'];
        return in_array($user->role->name, $allowedRoles);
    }

    public function destroyPost(User $user): bool
    {
        $allowedRoles = ['employee', 'manager'];
        return in_array($user->role->name, $allowedRoles);
    }

}

