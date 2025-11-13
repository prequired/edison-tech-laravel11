<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy for Task model authorization.
 *
 * @package App\Policies
 */
class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     *
     * @param User $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        // Admins and employees can view all tasks
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Clients can view tasks (will be filtered to assigned ones)
        if ($user->isClient()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the task.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function view(User $user, Task $task): Response|bool
    {
        // Admins and employees can view all tasks
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Users can view tasks assigned to them
        if ($task->assigned_to === $user->id) {
            return true;
        }

        // Users can view tasks they created
        if ($task->created_by === $user->id) {
            return true;
        }

        return Response::deny('You do not have permission to view this task.');
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        // Admins and employees can create tasks
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function update(User $user, Task $task): Response|bool
    {
        // Admins and employees can update all tasks
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        // Users can update tasks assigned to them (with restrictions)
        if ($task->assigned_to === $user->id) {
            return true;
        }

        return Response::deny('You do not have permission to update this task.');
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function delete(User $user, Task $task): Response|bool
    {
        // Only admins and employees can delete tasks
        if ($user->isAdmin() || $user->isEmployee()) {
            return true;
        }

        return Response::deny('You do not have permission to delete tasks.');
    }

    /**
     * Determine whether the user can restore the task.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function restore(User $user, Task $task): Response|bool
    {
        // Only admins and employees can restore tasks
        return $user->isAdmin() || $user->isEmployee();
    }

    /**
     * Determine whether the user can permanently delete the task.
     *
     * @param User $user
     * @param Task $task
     * @return Response|bool
     */
    public function forceDelete(User $user, Task $task): Response|bool
    {
        // Only admins can force delete tasks
        return $user->isAdmin();
    }
}
