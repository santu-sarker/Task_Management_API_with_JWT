<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : Response::deny('You do not have access to view this task.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : Response::deny('You do not have access to edit this task.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : Response::deny('You do not have access to delete this task.');
    }
}
