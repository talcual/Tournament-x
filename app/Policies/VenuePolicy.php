<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;

class VenuePolicy
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
        return $user->can('view venues');
    }

    public function view(User $user, Venue $venue): bool
    {
        return $user->can('view venues');
    }

    public function create(User $user): bool
    {
        return $user->can('create venues');
    }

    public function update(User $user, Venue $venue): bool
    {
        return $user->can('update venues');
    }

    public function delete(User $user, Venue $venue): bool
    {
        return $user->can('delete venues');
    }
}
