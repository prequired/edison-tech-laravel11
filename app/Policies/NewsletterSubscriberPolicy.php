<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\NewsletterSubscriber;
use App\Models\User;

/**
 * Newsletter Subscriber Policy
 *
 * Authorization logic for NewsletterSubscriber operations.
 */
class NewsletterSubscriberPolicy
{
    /**
     * Determine whether the user can view any newsletter subscribers.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can view the newsletter subscriber.
     *
     * @param User $user
     * @param NewsletterSubscriber $newsletterSubscriber
     * @return bool
     */
    public function view(User $user, NewsletterSubscriber $newsletterSubscriber): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can create newsletter subscribers.
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user): bool
    {
        return true; // Public can subscribe
    }

    /**
     * Determine whether the user can update the newsletter subscriber.
     *
     * @param User $user
     * @param NewsletterSubscriber $newsletterSubscriber
     * @return bool
     */
    public function update(User $user, NewsletterSubscriber $newsletterSubscriber): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the newsletter subscriber.
     *
     * @param User $user
     * @param NewsletterSubscriber $newsletterSubscriber
     * @return bool
     */
    public function delete(User $user, NewsletterSubscriber $newsletterSubscriber): bool
    {
        return $user->isAdmin();
    }
}
