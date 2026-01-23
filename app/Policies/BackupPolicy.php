<?php

namespace App\Policies;

use App\Models\Backup;
use App\Models\User;

class BackupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Backup $backup): bool
    {
        // if the user owns the vault or if he got invited to another vault (need to check how to do that)
        return $user->id === $backup->vault->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Backup $backup): bool
    {
        return ! $backup->vault->delete_protection;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Backup $backup): bool
    {
        // check wether vault has WORM protection enabled
        return ! $backup->vault->delete_protection;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Backup $backup): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Backup $backup): bool
    {
        return ! $backup->vault->delete_protection;
    }
}
