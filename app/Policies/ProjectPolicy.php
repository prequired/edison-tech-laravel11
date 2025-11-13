<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for Project model authorization.
 *
 * @package App\Policies
 */
class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins and employees can view all projects
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can view projects list (will be filtered to their own)
        if ($user->isClient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the project.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function view(User $user, Project $project): Response|bool
    {
        // Admins and employees can view all projects
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can only view their own company's projects
        if ($user->isClient() && $user->company_id === $project->company_id) {
            return true;
        }

        return Response::deny('You do not have permission to view this project.');
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Only admins and employees can create projects
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function update(User $user, Project $project): Response|bool
    {
        // Only admins and employees can update projects
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function delete(User $user, Project $project): Response|bool
    {
        // Only admins can delete projects
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the project.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function restore(User $user, Project $project): Response|bool
    {
        // Only admins can restore projects
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the project.
     *
     * @param User $user
     * @param Project $project
     * @return Response|bool
     */
    public function forceDelete(User $user, Project $project): Response|bool
    {
        // Only admins can force delete projects
        return $user->isAdmin();
    }
}
