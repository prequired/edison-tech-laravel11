<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for Invoice model authorization.
 *
 * @package App\Policies
 */
class InvoicePolicy
{
    /**
     * Determine whether the user can view any invoices.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins and employees can view all invoices
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can view invoices list (will be filtered to their own)
        if ($user->isClient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the invoice.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return Response|bool
     */
    public function view(User $user, Invoice $invoice): Response|bool
    {
        // Admins and employees can view all invoices
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can only view their own company's invoices
        if ($user->isClient() && $user->company_id === $invoice->company_id) {
            return true;
        }

        return Response::deny('You do not have permission to view this invoice.');
    }

    /**
     * Determine whether the user can create invoices.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Only admins and employees can create invoices
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the invoice.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return Response|bool
     */
    public function update(User $user, Invoice $invoice): Response|bool
    {
        // Only admins and employees can update invoices
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        return Response::deny('You do not have permission to update invoices.');
    }

    /**
     * Determine whether the user can delete the invoice.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return Response|bool
     */
    public function delete(User $user, Invoice $invoice): Response|bool
    {
        // Only admins can delete invoices
        if ($user->isAdmin()) {
            return true;
        }

        return Response::deny('You do not have permission to delete invoices.');
    }

    /**
     * Determine whether the user can restore the invoice.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return Response|bool
     */
    public function restore(User $user, Invoice $invoice): Response|bool
    {
        // Only admins can restore invoices
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the invoice.
     *
     * @param User $user
     * @param Invoice $invoice
     * @return Response|bool
     */
    public function forceDelete(User $user, Invoice $invoice): Response|bool
    {
        // Only admins can force delete invoices
        return $user->isAdmin();
    }
}
