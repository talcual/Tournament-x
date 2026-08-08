<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;

class PlayerPolicy
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
        return $user->can('view players');
    }

    public function view(User $user, Player $player): bool
    {
        return $user->can('view players');
    }

    public function create(User $user): bool
    {
        return $user->can('create players');
    }

    public function update(User $user, Player $player): bool
    {
        return $user->can('update players');
    }

    public function delete(User $user, Player $player): bool
    {
        return $user->can('delete players');
    }
}
