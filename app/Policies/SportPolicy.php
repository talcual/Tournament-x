<?php

namespace App\Policies;

use App\Models\Sport;
use App\Models\User;

class SportPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view sports');
    }

    public function view(User $user, Sport $sport): bool
    {
        return $user->can('view sports');
    }

    public function create(User $user): bool
    {
        return $user->can('create sports');
    }

    public function update(User $user, Sport $sport): bool
    {
        return $user->can('update sports');
    }

    public function delete(User $user, Sport $sport): bool
    {
        return $user->can('delete sports');
    }
}
