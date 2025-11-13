<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PortfolioItem;
use App\Models\User;

/**
 * Portfolio Item Policy
 *
 * Authorization logic for PortfolioItem operations.
 */
class PortfolioItemPolicy
{
    /**
     * Determine whether the user can view any portfolio items.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the portfolio item.
     *
     * @param User $user
     * @param PortfolioItem $portfolioItem
     * @return bool
     */
    public function view(User $user, PortfolioItem $portfolioItem): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can create portfolio items.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the portfolio item.
     *
     * @param User $user
     * @param PortfolioItem $portfolioItem
     * @return bool
     */
    public function update(User $user, PortfolioItem $portfolioItem): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can delete the portfolio item.
     *
     * @param User $user
     * @param PortfolioItem $portfolioItem
     * @return bool
     */
    public function delete(User $user, PortfolioItem $portfolioItem): bool
    {
        return $user->isAdmin();
    }
}
