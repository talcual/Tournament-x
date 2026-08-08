<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
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
        return $user->can('view tournaments');
    }

    public function view(User $user, Tournament $tournament): bool
    {
        if ($user->id === $tournament->organizer_id) {
            return true;
        }

        return $user->can('view tournaments');
    }

    public function create(User $user): bool
    {
        return $user->can('create tournaments');
    }

    public function update(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->organizer_id || $user->can('update tournaments');
    }

    public function delete(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->organizer_id || $user->can('delete tournaments');
    }
}
