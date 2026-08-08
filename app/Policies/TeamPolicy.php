<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
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
        return $user->can('view teams');
    }

    public function view(User $user, Team $team): bool
    {
        return $user->can('view teams');
    }

    public function create(User $user): bool
    {
        return $user->can('create teams');
    }

    public function update(User $user, Team $team): bool
    {
        return $user->can('update teams');
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->can('delete teams');
    }
}
