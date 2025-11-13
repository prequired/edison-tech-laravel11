<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for Company model authorization.
 *
 * @package App\Policies
 */
class CompanyPolicy
{
    /**
     * Determine whether the user can view any companies.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Only admins can view all companies
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the company.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function view(User $user, Company $company): Response|bool
    {
        // Admins can view all companies
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view their own company
        if ($user->company_id === $company->id) {
            return true;
        }

        return Response::deny('You do not have permission to view this company.');
    }

    /**
     * Determine whether the user can create companies.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Only admins can create companies
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the company.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function update(User $user, Company $company): Response|bool
    {
        // Only admins can update companies
        if ($user->isAdmin()) {
            return true;
        }

        return Response::deny('You do not have permission to update companies.');
    }

    /**
     * Determine whether the user can delete the company.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function delete(User $user, Company $company): Response|bool
    {
        // Only admins can delete companies
        if ($user->isAdmin()) {
            return true;
        }

        return Response::deny('You do not have permission to delete companies.');
    }

    /**
     * Determine whether the user can restore the company.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function restore(User $user, Company $company): Response|bool
    {
        // Only admins can restore companies
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the company.
     *
     * @param User $user
     * @param Company $company
     * @return Response|bool
     */
    public function forceDelete(User $user, Company $company): Response|bool
    {
        // Only admins can force delete companies
        return $user->isAdmin();
    }
}
