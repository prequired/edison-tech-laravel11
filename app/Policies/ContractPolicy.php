<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for Contract model authorization.
 *
 * @package App\Policies
 */
class ContractPolicy
{
    /**
     * Determine whether the user can view any contracts.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins and employees can view all contracts
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can view contracts list (will be filtered to their own)
        if ($user->isClient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the contract.
     *
     * @param User $user
     * @param Contract $contract
     * @return Response|bool
     */
    public function view(User $user, Contract $contract): Response|bool
    {
        // Admins and employees can view all contracts
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can only view their own company's contracts
        if ($user->isClient() && $user->company_id === $contract->company_id) {
            return true;
        }

        return Response::deny('You do not have permission to view this contract.');
    }

    /**
     * Determine whether the user can create contracts.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Only admins and employees can create contracts
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the contract.
     *
     * @param User $user
     * @param Contract $contract
     * @return Response|bool
     */
    public function update(User $user, Contract $contract): Response|bool
    {
        // Only admins and employees can update contracts
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        return Response::deny('You do not have permission to update contracts.');
    }

    /**
     * Determine whether the user can delete the contract.
     *
     * @param User $user
     * @param Contract $contract
     * @return Response|bool
     */
    public function delete(User $user, Contract $contract): Response|bool
    {
        // Only admins can delete contracts
        if ($user->isAdmin()) {
            return true;
        }

        return Response::deny('You do not have permission to delete contracts.');
    }

    /**
     * Determine whether the user can restore the contract.
     *
     * @param User $user
     * @param Contract $contract
     * @return Response|bool
     */
    public function restore(User $user, Contract $contract): Response|bool
    {
        // Only admins can restore contracts
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the contract.
     *
     * @param User $user
     * @param Contract $contract
     * @return Response|bool
     */
    public function forceDelete(User $user, Contract $contract): Response|bool
    {
        // Only admins can force delete contracts
        return $user->isAdmin();
    }
}
