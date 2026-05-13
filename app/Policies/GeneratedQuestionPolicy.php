<?php

namespace App\Policies;

use App\Models\GeneratedQuestion;
use App\Models\User;

class GeneratedQuestionPolicy
{

    public function view(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }
}
