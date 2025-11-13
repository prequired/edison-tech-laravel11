<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogPost;
use App\Models\User;

/**
 * Blog Post Policy
 *
 * Authorization logic for BlogPost operations.
 */
class BlogPostPolicy
{
    /**
     * Determine whether the user can view any blog posts.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the blog post.
     *
     * @param User $user
     * @param BlogPost $blogPost
     * @return bool
     */
    public function view(User $user, BlogPost $blogPost): bool
    {
        return $user->isAdmin() || $user->isEmployee() || $blogPost->author_id === $user->id;
    }

    /**
     * Determine whether the user can create blog posts.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the blog post.
     *
     * @param User $user
     * @param BlogPost $blogPost
     * @return bool
     */
    public function update(User $user, BlogPost $blogPost): bool
    {
        return $user->isAdmin() || $blogPost->author_id === $user->id;
    }

    /**
     * Determine whether the user can delete the blog post.
     *
     * @param User $user
     * @param BlogPost $blogPost
     * @return bool
     */
    public function delete(User $user, BlogPost $blogPost): bool
    {
        return $user->isAdmin();
    }
}
