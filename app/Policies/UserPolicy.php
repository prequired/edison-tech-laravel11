<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for User model authorization.
 *
 * @package App\Policies
 */
class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins can view all users
        if ($user->isAdmin()) {
            return true;
        }

        // Employees can view users from their company
        if ($user->isEmployee()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function view(User $user, User $model): Response|bool
    {
        // Admins can view all users
        if ($user->isAdmin()) {
            return true;
        }

        // Users can view themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Employees can view users from the same company
        if ($user->isEmployee() && $user->company_id === $model->company_id) {
            return true;
        }

        return Response::deny('You do not have permission to view this user.');
    }

    /**
     * Determine whether the user can create users.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Only admins can create users
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function update(User $user, User $model): Response|bool
    {
        // Admins can update all users
        if ($user->isAdmin()) {
            return true;
        }

        // Users can update themselves (with restrictions on certain fields)
        if ($user->id === $model->id) {
            return true;
        }

        return Response::deny('You do not have permission to update this user.');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function delete(User $user, User $model): Response|bool
    {
        // Admins can delete users (except themselves)
        if ($user->isAdmin() && $user->id !== $model->id) {
            return true;
        }

        return Response::deny('You do not have permission to delete this user.');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function restore(User $user, User $model): Response|bool
    {
        // Only admins can restore users
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function forceDelete(User $user, User $model): Response|bool
    {
        // Only admins can force delete users (except themselves)
        if ($user->isAdmin() && $user->id !== $model->id) {
            return true;
        }

        return false;
    }
}
