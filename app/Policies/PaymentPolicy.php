<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for Payment model authorization.
 *
 * @package App\Policies
 */
class PaymentPolicy
{
    /**
     * Determine whether the user can view any payments.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins and employees can view all payments
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can view payments list (will be filtered to their own)
        if ($user->isClient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the payment.
     *
     * @param User $user
     * @param Payment $payment
     * @return Response|bool
     */
    public function view(User $user, Payment $payment): Response|bool
    {
        // Admins and employees can view all payments
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can only view their own company's payments
        if ($user->isClient() && $user->company_id === $payment->company_id) {
            return true;
        }

        return Response::deny('You do not have permission to view this payment.');
    }

    /**
     * Determine whether the user can create payments.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Admins, employees, and clients can create payments
        return $user->isAdmin() || $user->isEmployee() || $user->isClient();
    }

    /**
     * Determine whether the user can update the payment.
     *
     * @param User $user
     * @param Payment $payment
     * @return Response|bool
     */
    public function update(User $user, Payment $payment): Response|bool
    {
        // Only admins and employees can update payments
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        return Response::deny('You do not have permission to update payments.');
    }

    /**
     * Determine whether the user can delete the payment.
     *
     * @param User $user
     * @param Payment $payment
     * @return Response|bool
     */
    public function delete(User $user, Payment $payment): Response|bool
    {
        // Only admins can delete payments
        if ($user->isAdmin()) {
            return true;
        }

        return Response::deny('You do not have permission to delete payments.');
    }

    /**
     * Determine whether the user can restore the payment.
     *
     * @param User $user
     * @param Payment $payment
     * @return Response|bool
     */
    public function restore(User $user, Payment $payment): Response|bool
    {
        // Only admins can restore payments
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the payment.
     *
     * @param User $user
     * @param Payment $payment
     * @return Response|bool
     */
    public function forceDelete(User $user, Payment $payment): Response|bool
    {
        // Only admins can force delete payments
        return $user->isAdmin();
    }
}
