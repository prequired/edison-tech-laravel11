<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * Document Policy
 *
 * Authorization logic for Document operations.
 */
class DocumentPolicy
{
    /**
     * Determine whether the user can view any documents.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view documents list
    }

    /**
     * Determine whether the user can view the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function view(User $user, Document $document): bool
    {
        // Admins and employees can view all documents
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can view public documents or documents belonging to their company
        if ($document->is_public) {
            return true;
        }

        // Check if document belongs to user's company
        if ($document->documentable_type === 'App\Models\Company') {
            return $document->documentable_id === $user->company_id;
        }

        // Check if document belongs to a project of user's company
        if ($document->documentable_type === 'App\Models\Project') {
            $project = $document->documentable;
            return $project && $project->company_id === $user->company_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create documents.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function update(User $user, Document $document): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can delete the document.
     *
     * @param User $user
     * @param Document $document
     * @return bool
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }
}
