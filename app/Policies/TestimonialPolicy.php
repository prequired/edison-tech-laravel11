<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Testimonial;
use App\Models\User;

/**
 * Testimonial Policy
 *
 * Authorization logic for Testimonial operations.
 */
class TestimonialPolicy
{
    /**
     * Determine whether the user can view any testimonials.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the testimonial.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function view(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can create testimonials.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the testimonial.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can delete the testimonial.
     *
     * @param User $user
     * @param Testimonial $testimonial
     * @return bool
     */
    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->isAdmin();
    }
}
