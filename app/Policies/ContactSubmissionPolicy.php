<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ContactSubmission;
use App\Models\User;

/**
 * Contact Submission Policy
 *
 * Authorization logic for ContactSubmission operations.
 */
class ContactSubmissionPolicy
{
    /**
     * Determine whether the user can view any contact submissions.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the contact submission.
     *
     * @param User $user
     * @param ContactSubmission $contactSubmission
     * @return bool
     */
    public function view(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can create contact submissions.
     *
     * Public can create, no authentication required
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user): bool
    {
        return true; // Public can submit contact forms
    }

    /**
     * Determine whether the user can update the contact submission.
     *
     * @param User $user
     * @param ContactSubmission $contactSubmission
     * @return bool
     */
    public function update(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can delete the contact submission.
     *
     * @param User $user
     * @param ContactSubmission $contactSubmission
     * @return bool
     */
    public function delete(User $user, ContactSubmission $contactSubmission): bool
    {
        return $user->isAdmin();
    }
}
