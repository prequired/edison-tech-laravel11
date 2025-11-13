<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogCategory;
use App\Models\User;

/**
 * Blog Category Policy
 *
 * Authorization logic for BlogCategory operations.
 */
class BlogCategoryPolicy
{
    /**
     * Determine whether the user can view any blog categories.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the blog category.
     *
     * @param User $user
     * @param BlogCategory $blogCategory
     * @return bool
     */
    public function view(User $user, BlogCategory $blogCategory): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can create blog categories.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the blog category.
     *
     * @param User $user
     * @param BlogCategory $blogCategory
     * @return bool
     */
    public function update(User $user, BlogCategory $blogCategory): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the blog category.
     *
     * @param User $user
     * @param BlogCategory $blogCategory
     * @return bool
     */
    public function delete(User $user, BlogCategory $blogCategory): bool
    {
        return $user->isAdmin();
    }
}
