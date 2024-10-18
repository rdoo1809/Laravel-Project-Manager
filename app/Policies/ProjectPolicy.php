<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function create(User $user): bool
    {
        return $user->is_manager;
    }

    public function store(User $user): bool
    {
        return $user->is_manager;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->is_manager;
    }

    public function view(User $user, Project $project): bool
    {
        return $user->is_manager;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->is_manager;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_manager;
    }


    /**
     * Determine whether the user can restore the model.
     */
//    public function restore(User $user, Project $project): bool
//    {
//        //
//    }

    /**
     * Determine whether the user can permanently delete the model.
     */
//    public function forceDelete(User $user, Project $project): bool
//    {
//        //
//    }
}
