<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    // Grants admins all application abilities.
    public function before(?User $user, string $ability): ?bool
    {
        if ($user?->isAdmin()) {
            return true;
        }

        return null;
    }

    // Allows candidates to list their applications.
    public function viewAny(User $user): bool
    {
        return $user->isCandidate();
    }

    // Allows candidates to delete their own applications.
    public function delete(User $user, Application $application): bool
    {
        return $user->isCandidate() && $user->id === $application->candidate_id;
    }

    // Allows job owners to update application status.
    public function updateStatus(User $user, Application $application): bool
    {
        return $user->isEmployer()
            && $application->job()->where('employer_id', $user->id)->exists();
    }
}
