<?php

namespace App\Policies;

use App\Models\Concept;
use App\Models\Domain;
use App\Models\User;

class ConceptPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Concept $concept): bool
    {
        return $concept->domain && $user->id === $concept->domain->user_id;
    }

    public function create(User $user, Domain $domain): bool
    {
        return $user->id === $domain->user_id;
    }

    public function update(User $user, Concept $concept): bool
    {
        return $concept->domain && $user->id === $concept->domain->user_id;
    }

    public function delete(User $user, Concept $concept): bool
    {
        return $concept->domain && $user->id === $concept->domain->user_id;
    }

    public function restore(User $user, Concept $concept): bool
    {
        return $concept->domain && $user->id === $concept->domain->user_id;
    }

    public function forceDelete(User $user, Concept $concept): bool
    {
        return $concept->domain && $user->id === $concept->domain->user_id;
    }
}
