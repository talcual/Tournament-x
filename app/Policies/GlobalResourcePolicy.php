<?php

namespace App\Policies;

use App\Models\User;

class GlobalResourcePolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user, string $resource): bool
    {
        return $user->can("view {$resource}");
    }

    public function create(User $user, string $resource): bool
    {
        return $user->can("create {$resource}");
    }

    public function update(User $user, string $resource): bool
    {
        return $user->can("update {$resource}");
    }

    public function delete(User $user, string $resource): bool
    {
        return $user->can("delete {$resource}");
    }
}
